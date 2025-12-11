@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="md:flex">
            <!-- Lewa strona: Zdjęcie -->
            <div class="md:w-1/2 bg-gray-200 min-h-[300px] relative">
                @if($car->image_path)
                    <img src="{{ asset('storage/' . $car->image_path) }}" alt="{{ $car->brand->name }} {{ $car->model }}" class="w-full h-full object-cover absolute inset-0">
                @else
                    <div class="flex items-center justify-center h-full text-gray-400 flex-col">
                        <span class="text-6xl">🚗</span>
                        <span class="mt-2 text-sm font-medium">Brak zdjęcia</span>
                    </div>
                @endif
            </div>

            <!-- Prawa strona: Informacje -->
            <div class="md:w-1/2 p-8">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $car->brand->name }} {{ $car->model }}</h1>
                        <p class="text-sm text-gray-500 mt-1">Rok produkcji: {{ $car->year }}</p>
                    </div>
                    <div class="text-right">
                        <span class="block text-3xl font-bold text-blue-600">{{ number_format($car->daily_rate, 0) }} zł</span>
                        <span class="text-gray-500 text-sm">za dobę</span>
                    </div>
                </div>

                <hr class="my-6 border-gray-100">

                <div class="grid grid-cols-2 gap-4 mb-6 text-sm">
                    <div>
                        <span class="block text-gray-500">Typ nadwozia</span>
                        <span class="font-semibold text-gray-800">{{ $car->type->name }}</span>
                    </div>
                    <div>
                        <span class="block text-gray-500">Przebieg</span>
                        <span class="font-semibold text-gray-800">{{ number_format($car->mileage, 0, ' ', ' ') }} km</span>
                    </div>
                    <div>
                        <span class="block text-gray-500">Kolor</span>
                        <span class="font-semibold text-gray-800">{{ $car->color }}</span>
                    </div>
                    <div>
                        <span class="block text-gray-500">Lokalizacja</span>
                        <span class="font-semibold text-gray-800">{{ $car->branch->city }} ({{ $car->branch->name }})</span>
                    </div>
                </div>

                <div class="mb-8">
                    <h3 class="text-sm font-medium text-gray-500 mb-2 uppercase tracking-wide">Wyposażenie</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($car->features as $feature)
                            <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-medium border border-gray-200">
                                {{ $feature->name }}
                            </span>
                        @endforeach
                    </div>
                </div>

                <div class="flex gap-4">
                    <a href="{{ route('cars.index') }}" class="w-1/3 text-center px-4 py-3 border border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 transition">
                        Wróć
                    </a>
                    
                    @if($car->is_available)
                        {{-- Tutaj w przyszłości dodasz link do formularza rezerwacji --}}
                        <button type="button" class="w-2/3 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700 transition shadow-md hover:shadow-lg transform active:scale-95" onclick="alert('Funkcja rezerwacji będzie dostępna w kolejnym kroku!')">
                            Rezerwuj teraz
                        </button>
                    @else
                        <button disabled class="w-2/3 bg-gray-300 text-gray-500 rounded-lg font-bold cursor-not-allowed">
                            Obecnie niedostępny
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection