<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use App\Models\RentalStatus;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * Dashboard pracownika - lista rezerwacji.
     */
    public function index(Request $request)
    {
        $query = Rental::with(['user', 'car.brand', 'status'])->latest();

        // Filtrowanie po statusie
        if ($request->has('status')) {
            $query->whereHas('status', function ($q) use ($request) {
                $q->where('name', $request->status);
            });
        }

        $rentals = $query->paginate(15);

        // Do przycisków filtrów
        $statuses = RentalStatus::all();

        return view('employee.dashboard', compact('rentals', 'statuses'));
    }

    /**
     * Zmiana statusu rezerwacji (np. Potwierdzenie, Wydanie, Zwrot).
     */
    public function updateStatus(Request $request, Rental $rental)
    {
        $request->validate([
            'status' => 'required|exists:rental_statuses,name'
        ]);

        $status = RentalStatus::where('name', $request->status)->first();
        $rental->update(['rental_status_id' => $status->id]);

        // Jeśli status to 'ongoing' (Wydanie auta) -> można dodać logikę oznaczania auta jako niedostępne
        // Jeśli status to 'completed' (Zwrot) -> można oznaczyć jako dostępne

        return back()->with('success', "Status rezerwacji #{$rental->id} został zmieniony na: {$status->label}");
    }
}
