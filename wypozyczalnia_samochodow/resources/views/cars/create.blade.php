@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Dodaj nowy samochód</h2>

    <form action="{{ route('cars.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Marka i Model -->
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Marka</label>
                <select name="brand_id" class="w-full border-gray-300 rounded-md shadow-sm">
                    @foreach($brands as $brand)
                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                    @endforeach
                </select>
            </div>
             <div>
                <label class="block text-sm font-medium text-gray-700">Model</label>
                <input type="text" name="model" required class="w-full border-gray-300 rounded-md shadow-sm">
            </div>
        </div>
        
        <!-- Typ i Oddział -->
        <div class="grid grid-cols-2 gap-4 mb-4">
             <div>
                <label class="block text-sm font-medium text-gray-700">Typ</label>
                <select name="car_type_id" class="w-full border-gray-300 rounded-md shadow-sm">
                    @foreach($types as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
             <div>
                <label class="block text-sm font-medium text-gray-700">Oddział</label>
                <select name="branch_id" class="w-full border-gray-300 rounded-md shadow-sm">
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->city }} ({{ $branch->name }})</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Szczegóły: Rocznik, Kolor, Przebieg -->
        <div class="grid grid-cols-3 gap-4 mb-4">
             <div>
                 <label class="block text-sm font-medium text-gray-700">Rocznik</label>
                 <input type="number" name="year" value="{{ date('Y') }}" min="1900" max="{{ date('Y') + 1 }}" class="w-full border-gray-300 rounded-md shadow-sm">
             </div>
             
             <!-- KOLOR JAKO SELECT -->
             <div>
                 <label class="block text-sm font-medium text-gray-700">Kolor</label>
                 <select name="color" class="w-full border-gray-300 rounded-md shadow-sm">
                     <option value="Biały">Biały</option>
                     <option value="Czarny">Czarny</option>
                     <option value="Srebrny">Srebrny</option>
                     <option value="Szary">Szary</option>
                     <option value="Czerwony">Czerwony</option>
                     <option value="Niebieski">Niebieski</option>
                     <option value="Granatowy">Granatowy</option>
                     <option value="Brązowy">Brązowy</option>
                     <option value="Beżowy">Beżowy</option>
                     <option value="Zielony">Zielony</option>
                     <option value="Żółty">Żółty</option>
                     <option value="Inny">Inny</option>
                 </select>
             </div>

             <div>
                 <label class="block text-sm font-medium text-gray-700">Przebieg</label>
                 <input type="number" name="mileage" min="0" class="w-full border-gray-300 rounded-md shadow-sm">
             </div>
        </div>

        <!-- Finanse, Rejestracja i Skrzynia Biegów -->
        <div class="grid grid-cols-3 gap-4 mb-4">
             <div>
                 <label class="block text-sm font-medium text-gray-700">Cena za dobę</label>
                 <input type="number" step="0.01" name="daily_rate" min="0" required class="w-full border-gray-300 rounded-md shadow-sm">
             </div>
             <div>
                 <label class="block text-sm font-medium text-gray-700">Rejestracja</label>
                 <input type="text" name="registration_plate" required class="w-full border-gray-300 rounded-md shadow-sm uppercase">
             </div>
             <div>
                 <label class="block text-sm font-medium text-gray-700">Skrzynia biegów</label>
                 <select name="transmission" class="w-full border-gray-300 rounded-md shadow-sm">
                     <option value="manual">Manualna</option>
                     <option value="automatic">Automatyczna</option>
                 </select>
             </div>
        </div>

        <!-- Wyposażenie -->
        <div class="mb-6 border-t pt-4">
            <label class="block text-sm font-bold text-gray-700 mb-3">Wyposażenie pojazdu</label>
            <div class="grid grid-cols-2 gap-2">
                @foreach($features as $feature)
                    <div class="flex items-center">
                        <input type="checkbox" name="features[]" value="{{ $feature->id }}" id="feat_new_{{ $feature->id }}"
                            class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="feat_new_{{ $feature->id }}" class="ml-2 text-sm text-gray-700">
                            {{ $feature->name }}
                        </label>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Upload -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700">Zdjęcie</label>
            <input type="file" name="image" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:bg-blue-50 file:text-blue-700"/>
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700 transition">
            Dodaj samochód
        </button>
    </form>
</div>
@endsection