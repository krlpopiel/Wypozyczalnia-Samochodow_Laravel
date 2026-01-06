<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use App\Models\RentalStatus;
use App\Models\Car;
use App\Models\Branch;
use App\Models\Brand;   
use App\Models\Feature; 
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

    public function management()
    {
        $cars = Car::with(['brand', 'branch'])->paginate(10);
        $branches = Branch::all();
        $brands = Brand::all();     
        $features = Feature::all(); 
        
        return view('employee.management', compact('cars', 'branches', 'brands', 'features'));
    }

    public function storeBranch(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        Branch::create($request->all());
        return back()->with('success', 'Oddział został dodany.');
    }

    public function updateBranch(Request $request, Branch $branch)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        $branch->update($request->all());
        return back()->with('success', 'Dane oddziału zaktualizowane.');
    }

    public function destroyBranch(Branch $branch)
    {
        if ($branch->cars()->count() > 0) {
            return back()->withErrors(['branch' => 'Nie można usunąć oddziału, do którego przypisane są samochody.']);
        }
        $branch->delete();
        return back()->with('success', 'Oddział został usunięty.');
    }

    public function storeBrand(Request $request)
    {
        $request->validate(['name' => 'required|string|unique:brands,name']);
        Brand::create(['name' => $request->name]);
        return back()->with('success', 'Nowa marka została dodana.');
    }

    public function destroyBrand(Brand $brand)
    {
        if ($brand->cars()->count() > 0) return back()->withErrors(['brand' => 'Nie można usunąć marki, która ma przypisane auta.']);
        $brand->delete();
        return back()->with('success', 'Marka usunięta.');
    }

    public function storeFeature(Request $request)
    {
        $request->validate(['name' => 'required|string|unique:features,name']);
        Feature::create(['name' => $request->name]);
        return back()->with('success', 'Nowy element wyposażenia dodany.');
    }

    public function destroyFeature(Feature $feature)
    {
        $feature->cars()->detach();
        $feature->delete();
        return back()->with('success', 'Element wyposażenia usunięty.');
    }
}
