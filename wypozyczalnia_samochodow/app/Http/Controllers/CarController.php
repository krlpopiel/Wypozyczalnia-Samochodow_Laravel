<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\CarType;
use App\Models\Brand;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CarController extends Controller
{
    /**
     * Wyświetla listę samochodów z filtrowaniem dostępności.
     */
    public function index(Request $request)
    {
        $query = Car::with(['brand', 'type', 'branch', 'features'])
                    ->available(); // scopeAvailable z modelu Car (is_available = true)

        // 1. Filtr Marki
        if ($request->filled('brand')) {
            $query->where('brand_id', $request->brand);
        }

        // 2. Filtr Typu
        if ($request->filled('type')) {
            $query->where('car_type_id', $request->type);
        }

        // 3. Filtr Ceny
        if ($request->filled('max_price')) {
            $query->where('daily_rate', '<=', $request->max_price);
        }

        // 4. Filtr Daty (NOWOŚĆ) - Ukrywa zajęte auta
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start = Carbon::parse($request->start_date);
            $end = Carbon::parse($request->end_date);

            $query->whereDoesntHave('rentals', function ($q) use ($start, $end) {
                $q->whereHas('status', function ($sq) {
                    $sq->where('name', '!=', 'cancelled');
                })
                ->where(function ($dateQuery) use ($start, $end) {
                    // Ta sama logika co w RentalController - uwzględniamy przerwę
                    $dateQuery->whereDate('start_date', '<=', $end->copy()->addDay())
                              ->whereDate('end_date', '>=', $start->copy()->subDay());
                });
            });
        }

        $cars = $query->paginate(9)->withQueryString();

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