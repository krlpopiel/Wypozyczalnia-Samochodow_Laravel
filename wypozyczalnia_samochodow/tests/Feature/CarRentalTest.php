<?php

namespace Tests\Feature;

use App\Models\Car;
use App\Models\User;
use App\Models\Rental;
use App\Models\RentalStatus;
use App\Models\Branch;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarRentalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Uruchamiamy Seeder przed każdym testem, aby mieć dane (auta, statusy, oddziały)
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    /** * Test 1: Sprawdza, czy strona główna i lista samochodów ładują się poprawnie (HTTP 200).
     */
    public function test_homepage_and_car_list_are_accessible()
    {
        $response = $this->get(route('cars.index'));

        $response->assertStatus(200);
        $response->assertViewIs('cars.index');
        // Sprawdzamy czy widok zawiera kluczowe elementy (np. filtr "Filtrowanie")
        $response->assertSee('Filtrowanie'); 
    }

    /**
     * Test 2: Sprawdza proces rezerwacji przez zalogowanego klienta (Happy Path).
     * Weryfikuje utworzenie rekordu w bazie i obliczenie ceny.
     */
    public function test_authenticated_client_can_make_reservation()
    {
        // 1. Tworzymy klienta i pobieramy auto
        $user = User::factory()->create(['role' => 'client']);
        $car = Car::available()->first(); 

        // 2. Definiujemy daty
        $startDate = Carbon::tomorrow();
        $endDate = Carbon::tomorrow()->addDays(2); // 2 dni wynajmu
        
        // 3. Wysyłamy żądanie POST jako zalogowany użytkownik
        $response = $this->actingAs($user)->post(route('rentals.store', $car), [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'comments' => 'Testowa rezerwacja z PHPUnit',
        ]);

        // 4. Sprawdzamy przekierowanie (sukces)
        $response->assertRedirect(route('cars.index'));
        $response->assertSessionHas('success');

        // 5. Sprawdzamy czy rekord trafił do bazy z poprawną ceną
        $this->assertDatabaseHas('rentals', [
            'user_id' => $user->id,
            'car_id' => $car->id,
            'start_date' => $startDate->format('Y-m-d 00:00:00'),
            'total_price' => $car->daily_rate * 2, // 2 dni * stawka dzienna
        ]);
    }

    /**
     * Test 3: Sprawdza walidację dostępności.
     * Próba rezerwacji w terminie, który jest już zajęty, powinna zwrócić błąd.
     */
    public function test_cannot_reserve_car_in_occupied_dates()
    {
        $user = User::factory()->create(['role' => 'client']);
        $car = Car::first();
        $status = RentalStatus::where('name', 'confirmed')->first();

        // A. Tworzymy "zajętą" rezerwację w bazie (np. 10-15 stycznia)
        Rental::create([
            'user_id' => $user->id,
            'car_id' => $car->id,
            'rental_status_id' => $status->id,
            'start_date' => Carbon::parse('2025-01-10'),
            'end_date' => Carbon::parse('2025-01-15'),
            'total_price' => 500,
            'origin_branch_id' => $car->branch_id,
            'destination_branch_id' => $car->branch_id,
        ]);

        // B. Próba rezerwacji w kolidującym terminie (np. 12-13 stycznia)
        $response = $this->actingAs($user)->post(route('rentals.store', $car), [
            'start_date' => '2025-01-12',
            'end_date' => '2025-01-13',
        ]);

        // C. Oczekujemy błędu walidacji dla pola start_date
        $response->assertSessionHasErrors('start_date');
    }

    /**
     * Test 4: Sprawdza bezpieczeństwo (Middleware CheckRole).
     * Klient NIE może mieć dostępu do panelu pracownika.
     */
    public function test_client_cannot_access_employee_dashboard()
    {
        $client = User::factory()->create(['role' => 'client']);

        // Próba wejścia na dashboard pracownika
        $response = $this->actingAs($client)->get(route('employee.dashboard'));

        // Oczekujemy statusu 403 Forbidden
        $response->assertStatus(403);
    }

    /**
     * Test 5: Sprawdza uprawnienia pracownika.
     * Pracownik MOŻE zmienić status rezerwacji (np. na 'confirmed').
     */
    public function test_employee_can_update_rental_status()
    {
        $employee = User::factory()->create(['role' => 'employee']);
        
        // Tworzymy rezerwację testową
        $user = User::factory()->create();
        $car = Car::first();
        $statusPending = RentalStatus::where('name', 'pending')->first();
        
        $rental = Rental::create([
            'user_id' => $user->id,
            'car_id' => $car->id,
            'rental_status_id' => $statusPending->id,
            'start_date' => now(),
            'end_date' => now()->addDay(),
            'total_price' => 100,
            'origin_branch_id' => $car->branch_id,
            'destination_branch_id' => $car->branch_id,
        ]);

        // Pracownik zmienia status na 'confirmed'
        $response = $this->actingAs($employee)->patch(route('employee.rentals.status', $rental), [
            'status' => 'confirmed'
        ]);

        $response->assertSessionHas('success');
        
        // Weryfikacja zmiany w bazie
        $this->assertEquals('confirmed', $rental->fresh()->status->name);
    }

    /**
     * Test 6: Sprawdza logikę opłaty relokacyjnej (+100 zł).
     * Jeśli oddział zwrotu jest inny, cena powinna być wyższa.
     */
    public function test_relocation_fee_is_applied_when_branch_differs()
    {
        $user = User::factory()->create(['role' => 'client']);
        $car = Car::first();
        $branches = Branch::all();
        
        // Potrzebujemy co najmniej 2 oddziałów do tego testu
        if ($branches->count() < 2) {
            $this->markTestSkipped('Za mało oddziałów do testu relokacji.');
        }

        $originBranch = $branches[0];
        $destBranch = $branches[1];
        
        // Ustawiamy auto w oddziale A
        $car->update(['branch_id' => $originBranch->id]);

        $days = 3;
        
        // Rezerwujemy ze zwrotem w oddziale B
        $response = $this->actingAs($user)->post(route('rentals.store', $car), [
            'start_date' => Carbon::tomorrow()->format('Y-m-d'),
            'end_date' => Carbon::tomorrow()->addDays($days)->format('Y-m-d'),
            'diff_location' => 'on', // Checkbox zaznaczony
            'destination_branch_id' => $destBranch->id,
        ]);

        // Oczekiwana cena: (3 dni * stawka) + 100 zł
        $expectedPrice = ($days * $car->daily_rate) + 100;

        $this->assertDatabaseHas('rentals', [
            'user_id' => $user->id,
            'car_id' => $car->id,
            'destination_branch_id' => $destBranch->id,
            'total_price' => $expectedPrice,
        ]);
    }
}