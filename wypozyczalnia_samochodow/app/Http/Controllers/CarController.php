<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\CarType;
use App\Models\Brand;
use App\Models\Branch;
use App\Models\Feature;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class CarController extends Controller
{
    /**
     * Wyświetla listę samochodów z filtrowaniem dostępności.
     */
    public function index(Request $request)
    {
        $query = Car::with(['brand', 'type', 'branch', 'features'])->available();

        // 1. Filtr Marki
        if ($request->filled('brand')) {
            $query->where('brand_id', $request->brand);
        }

        // 2. Filtr Typu
        if ($request->filled('type')) {
            $query->where('car_type_id', $request->type);
        }

        // 3. Filtr Oddziału (Lokalizacja)
        if ($request->filled('branch')) {
            $query->where('branch_id', $request->branch);
        }

        // 4. Filtr Skrzyni Biegów
        if ($request->filled('transmission')) {
            $query->where('transmission', $request->transmission);
        }

        // 5. Filtr Ceny (Zakres)
        if ($request->filled('min_price')) {
            $query->where('daily_rate', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('daily_rate', '<=', $request->max_price);
        }

        // 6. Filtr Rocznika (Zakres)
        if ($request->filled('year_from')) {
            $query->where('year', '>=', $request->year_from);
        }
        if ($request->filled('year_to')) {
            $query->where('year', '<=', $request->year_to);
        }

        // 7. Filtr Wyposażenia (Musi zawierać WSZYSTKIE zaznaczone)
        if ($request->filled('features') && is_array($request->features)) {
            foreach ($request->features as $featureId) {
                $query->whereHas('features', function ($q) use ($featureId) {
                    $q->where('id', $featureId);
                });
            }
        }

        // 8. Filtr Daty (Dostępność)
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start = Carbon::parse($request->start_date);
            $end = Carbon::parse($request->end_date);

            $query->whereDoesntHave('rentals', function ($q) use ($start, $end) {
                $q->whereHas('status', function ($sq) {
                    $sq->where('name', '!=', 'cancelled');
                })
                ->where(function ($dateQuery) use ($start, $end) {
                    $dateQuery->whereDate('start_date', '<=', $end->copy()->addDay())
                              ->whereDate('end_date', '>=', $start->copy()->subDay());
                });
            });
        }

        $cars = $query->paginate(9)->withQueryString();

        // Dane do formularzy filtrów
        $brands = Brand::orderBy('name')->get();
        $types = CarType::all();
        $branches = Branch::all();
        $features = Feature::all();

        return view('cars.index', compact('cars', 'brands', 'types', 'branches', 'features'));
    }

    public function show(Car $car)
    {
        $car->load(['features', 'branch', 'brand', 'type']);
        return view('cars.show', compact('car'));
    }

    public function create()
    {
        $brands = Brand::all();
        $types = CarType::all();
        $branches = Branch::all();
        $features = Feature::all(); 
        
        return view('cars.create', compact('brands', 'types', 'branches', 'features'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'model' => 'required|string|max:255',
            'car_type_id' => 'required|exists:car_types,id',
            'branch_id' => 'required|exists:branches,id',
            'year' => 'required|integer|min:1900|max:' . (date('Y')),
            'color' => 'required|string',
            'daily_rate' => 'required|numeric|min:0',
            'registration_plate' => 'required|string|unique:cars,registration_plate',
            'mileage' => 'required|integer|min:0',
            'transmission' => 'required|in:manual,automatic',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'features' => 'array',
            'features.*' => 'exists:features,id',
        ]);

        $data = $request->except(['image', 'features']);
        
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('cars', 'public');
            $data['image_path'] = $path;
        }

        $car = Car::create($data);

        if ($request->has('features')) {
            $car->features()->sync($request->features);
        }

        return redirect()->route('cars.index')->with('success', 'Samochód został dodany do floty!');
    }

    public function edit(Car $car)
    {
        $brands = Brand::all();
        $types = CarType::all();
        $branches = Branch::all();
        $features = Feature::all(); 
        
        return view('cars.edit', compact('car', 'brands', 'types', 'branches', 'features'));
    }

     public function update(Request $request, Car $car)
    {
        $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'model' => 'required|string|max:255',
            'car_type_id' => 'required|exists:car_types,id',
            'branch_id' => 'required|exists:branches,id',
            'year' => 'required|integer|min:1900|max:' . (date('Y')),
            'color' => 'required|string',
            'daily_rate' => 'required|numeric|min:0',
            'registration_plate' => 'required|string|unique:cars,registration_plate,' . $car->id,
            'mileage' => 'required|integer|min:0',
            'transmission' => 'required|in:manual,automatic',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_available' => 'boolean',
            'features' => 'array',
            'features.*' => 'exists:features,id',
        ]);

        $data = $request->except(['image', 'features']);
        $data['is_available'] = $request->has('is_available');

        if ($request->hasFile('image')) {
            if ($car->image_path) {
                Storage::disk('public')->delete($car->image_path);
            }
            $path = $request->file('image')->store('cars', 'public');
            $data['image_path'] = $path;
        }

        $car->update($data);

        if ($request->has('features')) {
            $car->features()->sync($request->features);
        } else {
            $car->features()->detach();
        }

        return redirect()->route('employee.management')->with('success', 'Dane samochodu zaktualizowane.');
    }

    public function destroy(Car $car)
    {
        if ($car->rentals()->where('rental_status_id', 3)->exists()) {
             return back()->withErrors(['car' => 'Nie można usunąć auta, które jest obecnie wypożyczone.']);
        }
        
        if ($car->image_path) {
            Storage::disk('public')->delete($car->image_path);
        }
        
        $car->delete();
        return back()->with('success', 'Samochód został usunięty z floty.');
    }
}