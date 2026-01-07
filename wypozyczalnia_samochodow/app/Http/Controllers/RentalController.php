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
     * Zapisuje nową rezerwację z uwzględnieniem lokalizacji i opłaty.
     */
    public function store(Request $request, Car $car)
    {
        $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'diff_location' => 'nullable|string', 
            'destination_branch_id' => 'nullable|exists:branches,id',
        ], [
            'start_date.after_or_equal' => 'Data początkowa nie może być wcześniejsza niż dzisiaj.',
            'end_date.after_or_equal' => 'Data końcowa musi być równa lub późniejsza niż data początkowa.',
        ]);

        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);

        // 1. Sprawdzenie dostępności (z przerwą techniczną)
        $exists = Rental::where('car_id', $car->id)
            ->whereHas('status', function ($q) {
                $q->where('name', '!=', 'cancelled');
            })
            ->where(function ($query) use ($start, $end) {
                $query->whereDate('start_date', '<=', $end->copy()->addDay())
                      ->whereDate('end_date', '>=', $start->copy()->subDay());
            })
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'start_date' => 'Samochód jest niedostępny w wybranym terminie (wymagana przerwa techniczna).'
            ])->withInput();
        }

        // 2. Logika Lokalizacji
        $originBranchId = $car->branch_id; // Zawsze odbieramy tam gdzie stoi
        $destinationBranchId = $originBranchId; // Domyślnie oddajemy tam samo
        $extraFee = 0;

        // Jeśli zaznaczono "Zwrot w innej lokalizacji"
        if ($request->has('diff_location') && $request->filled('destination_branch_id')) {
            $destinationBranchId = $request->destination_branch_id;
            // Dolicz opłatę tylko jeśli faktycznie wybrano inny oddział
            if ($originBranchId != $destinationBranchId) {
                $extraFee = 100;
            }
        }

        // 3. Obliczenie ceny
        $days = $start->diffInDays($end);
        if ($days == 0) $days = 1;
        
        $totalPrice = ($days * $car->daily_rate) + $extraFee;

        // 4. Pobranie statusu
        $status = RentalStatus::where('name', 'pending')->firstOrFail();

        // 5. Zapis w bazie
        Rental::create([
            'user_id' => Auth::id(),
            'car_id' => $car->id,
            'rental_status_id' => $status->id,
            'start_date' => $start,
            'end_date' => $end,
            'total_price' => $totalPrice,
            'origin_branch_id' => $originBranchId,
            'destination_branch_id' => $destinationBranchId,
            'comments' => $request->comments,
        ]);

        return redirect()->route('cars.index')->with('success', 'Rezerwacja złożona pomyślnie! ' . ($extraFee > 0 ? '(Doliczono opłatę za zwrot w innym oddziale)' : ''));
    }

    public function index()
    {
        // Eager loading oddziałów dla wydajności
        $rentals = Rental::with(['car.brand', 'status', 'originBranch', 'destinationBranch'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('rentals.index', compact('rentals'));
    }
}