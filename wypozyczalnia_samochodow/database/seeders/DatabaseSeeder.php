<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Statusy (Słownik)
        $statuses = [
            ['name' => 'pending', 'label' => 'Oczekująca'],
            ['name' => 'confirmed', 'label' => 'Potwierdzona'],
            ['name' => 'ongoing', 'label' => 'W trakcie'],
            ['name' => 'completed', 'label' => 'Zakończona'],
            ['name' => 'cancelled', 'label' => 'Anulowana'],
        ];
        foreach ($statuses as $status) {
            DB::table('rental_statuses')->updateOrInsert(['name' => $status['name']], $status);
        }

        // 2. Oddziały (Słownik)
        $branches = [
            ['name' => 'Warszawa Centrum', 'address' => 'Marszałkowska 1', 'city' => 'Warszawa', 'phone' => '22 111 22 33'],
            ['name' => 'Kraków Lotnisko', 'address' => 'Kapitana Mieczysława Medweckiego 1', 'city' => 'Kraków', 'phone' => '12 333 44 55'],
            ['name' => 'Gdańsk Główny', 'address' => 'Podwale Grodzkie 2', 'city' => 'Gdańsk', 'phone' => '58 555 66 77'],
        ];
        foreach ($branches as $branch) {
            DB::table('branches')->updateOrInsert(['name' => $branch['name']], $branch);
        }

        // 3. Marki i Typy
        $brands = ['Toyota', 'BMW', 'Audi', 'Ford', 'Kia', 'Mercedes', 'Volvo', 'Mazda'];
        $brandIds = [];
        foreach($brands as $b) {
            $exists = DB::table('brands')->where('name', $b)->first();
            $brandIds[] = $exists ? $exists->id : DB::table('brands')->insertGetId(['name' => $b]);
        }

        $types = [
            ['name' => 'Sedan', 'slug' => 'sedan', 'base_multiplier' => 1.0],
            ['name' => 'SUV', 'slug' => 'suv', 'base_multiplier' => 1.3],
            ['name' => 'Kombi', 'slug' => 'station-wagon', 'base_multiplier' => 1.1],
            ['name' => 'Sport', 'slug' => 'sport', 'base_multiplier' => 2.0],
        ];
        $typeIds = [];
        foreach($types as $t) {
            $exists = DB::table('car_types')->where('slug', $t['slug'])->first();
            $typeIds[] = $exists ? $exists->id : DB::table('car_types')->insertGetId($t);
        }

        // 4. Cechy (Features)
        $featuresList = ['Klimatyzacja', 'GPS', 'Automat', 'Podgrzewane fotele', 'Tempomat', 'Bluetooth', 'Kamera cofania', 'Czujniki parkowania'];
        $featureIds = [];
        foreach($featuresList as $f) {
            $exists = DB::table('features')->where('name', $f)->first();
            $featureIds[] = $exists ? $exists->id : DB::table('features')->insertGetId(['name' => $f]);
        }

        // 5. Użytkownicy (Role)
        // Admin
        if (!User::where('email', 'admin@wypozyczalnia.pl')->exists()) {
            User::factory()->create([
                'name' => 'Administrator',
                'email' => 'admin@wypozyczalnia.pl',
                'role' => 'admin',
                'password' => Hash::make('password'),
            ]);
        }
        
        // Pracownik
        if (!User::where('email', 'pracownik@wypozyczalnia.pl')->exists()) {
            User::factory()->create([
                'name' => 'Jan Pracownik',
                'email' => 'pracownik@wypozyczalnia.pl',
                'role' => 'employee',
                'password' => Hash::make('password'),
            ]);
        }

        // Klient
        if (!User::where('email', 'klient@wypozyczalnia.pl')->exists()) {
            User::factory()->create([
                'name' => 'Anna Klient',
                'email' => 'klient@wypozyczalnia.pl',
                'role' => 'client',
                'password' => Hash::make('password'),
            ]);
        }

        // Dodatkowi klienci do generowania ruchu
        $clients = User::where('role', 'client')->get();
        if ($clients->count() < 5) {
            $clients = User::factory(10)->create(['role' => 'client']);
        }

        // 6. Samochody (30 sztuk)
        $dbBranches = DB::table('branches')->pluck('id')->toArray();
        $carsData = []; // Przechowujemy dane aut do generowania rezerwacji

        // Czyścimy tabelę aut przed seedowaniem (opcjonalne, ale w seedzie bezpieczniej)
        // Uwaga: truncate() może powodować błędy kluczy obcych, więc używamy insertGetId w pętli
        // Zakładamy, że baza jest czysta po migrate:fresh

        $existingCarsCount = DB::table('cars')->count();
        
        if ($existingCarsCount < 30) {
            for ($i = 1; $i <= 30; $i++) {
                $brandId = $brandIds[array_rand($brandIds)];
                $branchId = $dbBranches[array_rand($dbBranches)];
                $dailyRate = rand(100, 600);
                
                $carId = DB::table('cars')->insertGetId([
                    'brand_id' => $brandId,
                    'car_type_id' => $typeIds[array_rand($typeIds)],
                    'branch_id' => $branchId,
                    'model' => 'Model ' . chr(rand(65, 90)) . '-' . rand(100, 900),
                    'registration_plate' => 'WA ' . (20000 + $i), // Unikalne numery
                    'year' => rand(2019, 2024),
                    'color' => ['Czarny', 'Biały', 'Srebrny', 'Niebieski', 'Czerwony', 'Szary'][rand(0, 5)],
                    'mileage' => rand(1000, 150000),
                    'daily_rate' => $dailyRate,
                    'is_available' => true,
                    'transmission' => rand(0, 1) ? 'automatic' : 'manual',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Przypisz losowe cechy (2-4 cechy na auto)
                $randomFeatures = array_rand(array_flip($featureIds), rand(2, 4));
                if (!is_array($randomFeatures)) $randomFeatures = [$randomFeatures];
                
                foreach($randomFeatures as $fid) {
                    DB::table('car_feature')->insertOrIgnore([
                        'car_id' => $carId,
                        'feature_id' => $fid
                    ]);
                }

                $carsData[] = [
                    'id' => $carId,
                    'daily_rate' => $dailyRate,
                    'branch_id' => $branchId
                ];
            }
        } else {
            // Jeśli auta już są, pobieramy je
            $rawCars = DB::table('cars')->select('id', 'daily_rate', 'branch_id')->get();
            foreach($rawCars as $c) {
                $carsData[] = (array)$c;
            }
        }

        // 7. Rezerwacje, Płatności i Opinie (Generujemy historię)
        // Generujemy 50 rezerwacji w różnych stanach
        $rentalStatusesIds = DB::table('rental_statuses')->pluck('id', 'name');
        
        // Jeśli już są rezerwacje, nie duplikujemy za dużo
        if (DB::table('rentals')->count() < 10) {
            for ($j = 0; $j < 50; $j++) {
                $car = $carsData[array_rand($carsData)];
                $client = $clients->random();
                
                // Losowy status (ważone prawdopodobieństwo)
                $rand = rand(1, 100);
                if ($rand <= 10) $statusName = 'pending';
                elseif ($rand <= 30) $statusName = 'confirmed';
                elseif ($rand <= 40) $statusName = 'ongoing';
                elseif ($rand <= 50) $statusName = 'cancelled';
                else $statusName = 'completed'; // 50% szans na zakończone (żeby były opinie)

                // Daty (w przeszłości lub przyszłości zależnie od statusu)
                if ($statusName === 'completed' || $statusName === 'cancelled') {
                    $start = Carbon::now()->subDays(rand(5, 365));
                    $end = (clone $start)->addDays(rand(1, 14));
                } elseif ($statusName === 'ongoing') {
                    $start = Carbon::now()->subDays(rand(0, 5));
                    $end = Carbon::now()->addDays(rand(1, 10));
                } else {
                    $start = Carbon::now()->addDays(rand(1, 30));
                    $end = (clone $start)->addDays(rand(1, 14));
                }

                $days = $start->diffInDays($end) ?: 1;
                $totalPrice = $days * $car['daily_rate'];

                // Relokacja (10% szans)
                $originBranch = $car['branch_id'];
                $destBranch = $originBranch;
                if (rand(1, 10) == 1) {
                    $destBranch = $dbBranches[array_rand($dbBranches)];
                    if ($originBranch != $destBranch) $totalPrice += 100;
                }

                $rentalId = DB::table('rentals')->insertGetId([
                    'user_id' => $client->id,
                    'car_id' => $car['id'],
                    'rental_status_id' => $rentalStatusesIds[$statusName],
                    'start_date' => $start,
                    'end_date' => $end,
                    'total_price' => $totalPrice,
                    'origin_branch_id' => $originBranch,
                    'destination_branch_id' => $destBranch,
                    'comments' => rand(0, 1) ? 'Proszę o fotelik dziecięcy.' : null,
                    'created_at' => $start->subDays(2), // Rezerwacja zrobiona 2 dni wcześniej
                    'updated_at' => now(),
                ]);

                // Generowanie Płatności (dla nie-oczekujących)
                if ($statusName !== 'pending' && $statusName !== 'cancelled') {
                    DB::table('payments')->insert([
                        'rental_id' => $rentalId,
                        'amount' => $totalPrice,
                        'status' => 'paid',
                        'method' => ['card', 'transfer', 'blik'][rand(0, 2)],
                        'transaction_id' => 'TRX-' . strtoupper(uniqid()),
                        'created_at' => $start->subDay(),
                        'updated_at' => $start->subDay(),
                    ]);
                }

                // Generowanie Opinii (tylko dla zakończonych - 70% szans)
                if ($statusName === 'completed' && rand(1, 10) <= 7) {
                    DB::table('reviews')->insert([
                        'user_id' => $client->id,
                        'car_id' => $car['id'],
                        'rating' => rand(3, 5),
                        'comment' => ['Świetne auto!', 'Polecam, czysto i sprawnie.', 'Wszystko ok.', 'Trochę duże spalanie, ale wygodny.'][rand(0, 3)],
                        'created_at' => $end->addDay(),
                        'updated_at' => $end->addDay(),
                    ]);
                }
            }
        }
    }
}
