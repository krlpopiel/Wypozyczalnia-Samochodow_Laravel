<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Rental;
use App\Models\RentalStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RentalController extends Controller
{
    /**
     * Zapisuje nową rezerwację z uwzględnieniem przerwy technicznej.
     */
    public function store(Request $request, Car $car)
    {
        // 1. Walidacja danych wejściowych
        $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
        ], [
            'start_date.after_or_equal' => 'Data początkowa nie może być wcześniejsza niż dzisiaj.',
            'end_date.after_or_equal' => 'Data końcowa musi być równa lub późniejsza niż data początkowa.',
        ]);

        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);

        // 2. Sprawdzenie dostępności z uwzględnieniem 1 dnia przerwy
        // Logika: Nowa rezerwacja koliduje, jeśli jej zakres (poszerzony o 1 dzień marginesu)
        // nachodzi na istniejące rezerwacje.
        // Wzór kolizji: (StartA <= KoniecB + 1) AND (KoniecA >= StartB - 1)
        
        $exists = Rental::where('car_id', $car->id)
            ->whereHas('status', function ($q) {
                // Sprawdzamy wszystkie rezerwacje, które nie są anulowane
                $q->where('name', '!=', 'cancelled');
            })
            ->where(function ($query) use ($start, $end) {
                $query->whereDate('start_date', '<=', $end->copy()->addDay())
                      ->whereDate('end_date', '>=', $start->copy()->subDay());
            })
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'start_date' => 'Samochód jest niedostępny w wybranym terminie (wymagany jest też min. 1 dzień przerwy między wynajmami).'
            ])->withInput();
        }

        // 3. Obliczenie ceny
        $days = $start->diffInDays($end);
        if ($days == 0) $days = 1; // Minimum 1 dzień płatny
        
        $totalPrice = $days * $car->daily_rate;

        // 4. Pobranie statusu 'pending'
        $status = RentalStatus::where('name', 'pending')->firstOrFail();

        // 5. Zapis w bazie
        Rental::create([
            'user_id' => Auth::id(),
            'car_id' => $car->id,
            'rental_status_id' => $status->id,
            'start_date' => $start,
            'end_date' => $end,
            'total_price' => $totalPrice,
            'comments' => $request->comments,
        ]);

        return redirect()->route('cars.index')->with('success', 'Rezerwacja została złożona pomyślnie! Oczekuj na potwierdzenie.');
    }

    /**
     * Wyświetla rezerwacje zalogowanego klienta.
     */
    public function index()
    {
        $rentals = Rental::with(['car.brand', 'status'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('rentals.index', compact('rentals'));
    }
}