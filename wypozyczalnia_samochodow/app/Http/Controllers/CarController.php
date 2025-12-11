<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\CarType;
use App\Models\Brand;
use Illuminate\Http\Request;

class CarController extends Controller
{
    /**
     * Wyświetla listę samochodów dla klienta.
     * Dostępne filtrowanie.
     */
    public function index(Request $request)
    {
        $query = Car::with(['brand', 'type', 'branch', 'features'])
                    ->available();

        // Filtrowanie po marce
        if ($request->filled('brand')) {
            $query->where('brand_id', $request->brand);
        }

        // Filtrowanie po typie
        if ($request->filled('type')) {
            $query->where('car_type_id', $request->type);
        }

        // Filtrowanie po cenie (zakres)
        if ($request->filled('max_price')) {
            $query->where('daily_rate', '<=', $request->max_price);
        }

        $cars = $query->paginate(9)->withQueryString();

        // Dane do filtrów
        $brands = Brand::all();
        $types = CarType::all();

        return view('cars.index', compact('cars', 'brands', 'types'));
    }

    public function show(Car $car)
    {
        $car->load(['features', 'branch']);
        return view('cars.show', compact('car'));
    }
}