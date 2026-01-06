@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Edytuj samochód: {{ $car->brand->name }} {{ $car->model }}</h2>
        <a href="{{ route('employee.management') }}" class="text-gray-500 hover:text-gray-700">Wróć</a>
    </div>

    <form action="{{ route('cars.update', $car) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Marka i Model -->
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Marka</label>
                <select name="brand_id" class="w-full border-gray-300 rounded-md shadow-sm">
                    @foreach($brands as $brand)
                        <option value="{{ $brand->id }}" {{ $car->brand_id == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Model</label>
                <input type="text" name="model" value="{{ $car->model }}" required class="w-full border-gray-300 rounded-md shadow-sm">
            </div>
        </div>

        <!-- Typ i Oddział -->
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Typ nadwozia</label>
                <select name="car_type_id" class="w-full border-gray-300 rounded-md shadow-sm">
                    @foreach($types as $type)
                        <option value="{{ $type->id }}" {{ $car->car_type_id == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Oddział</label>
                <select name="branch_id" class="w-full border-gray-300 rounded-md shadow-sm">
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ $car->branch_id == $branch->id ? 'selected' : '' }}>{{ $branch->city }} ({{ $branch->name }})</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Szczegóły -->
        <div class="grid grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Rocznik</label>
                <input type="number" name="year" value="{{ $car->year }}" class="w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Kolor</label>
                <input type="text" name="color" value="{{ $car->color }}" class="w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Przebieg</label>
                <input type="number" name="mileage" value="{{ $car->mileage }}" class="w-full border-gray-300 rounded-md shadow-sm">
            </div>
        </div>

        <!-- Finanse i Rejestracja -->
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Cena za dobę (zł)</label>
                <input type="number" step="0.01" name="daily_rate" value="{{ $car->daily_rate }}" required class="w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Nr Rejestracyjny</label>
                <input type="text" name="registration_plate" value="{{ $car->registration_plate }}" required class="w-full border-gray-300 rounded-md shadow-sm uppercase">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Skrzynia biegów</label>
                <select name="transmission" class="w-full border-gray-300 rounded-md shadow-sm">
                    <option value="manual" {{ $car->transmission == 'manual' ? 'selected' : '' }}>Manualna</option>
                    <option value="automatic" {{ $car->transmission == 'automatic' ? 'selected' : '' }}>Automatyczna</option>
                </select>
            </div>
        </div>

        <div class="mb-6 border-t pt-4">
            <label class="block text-sm font-bold text-gray-700 mb-3">Wyposażenie pojazdu</label>
            <div class="grid grid-cols-2 gap-2">
                @foreach($features as $feature)
                    <div class="flex items-center">
                        <input type="checkbox" name="features[]" value="{{ $feature->id }}" id="feat_{{ $feature->id }}"
                            class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                            {{ $car->features->contains($feature->id) ? 'checked' : '' }}>
                        <label for="feat_{{ $feature->id }}" class="ml-2 text-sm text-gray-700">
                            {{ $feature->name }}
                        </label>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Status -->
        <div class="mb-6">
             <div class="flex items-center">
                <input id="is_available" name="is_available" type="checkbox" value="1" {{ $car->is_available ? 'checked' : '' }} class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <label for="is_available" class="ml-2 block text-sm text-gray-900">
                    Samochód dostępny do wypożyczenia (widoczny w ofercie)
                </label>
            </div>
        </div>

        <!-- Zdjęcie -->
        <div class="mb-6 border-t pt-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Zdjęcie samochodu</label>
            @if($car->image_path)
                <div class="mb-2">
                    <p class="text-xs text-gray-500 mb-1">Obecne zdjęcie:</p>
                    <img src="{{ asset('storage/' . $car->image_path) }}" alt="Aktualne zdjęcie" class="h-32 object-cover rounded">
                </div>
            @endif
            <input type="file" name="image" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"/>
        </div>

        <button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded-lg font-bold hover:bg-indigo-700 transition">
            Zapisz zmiany
        </button>
    </form>
</div>
@endsection