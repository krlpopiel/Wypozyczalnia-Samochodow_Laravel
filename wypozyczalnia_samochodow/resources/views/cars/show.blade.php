@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="md:flex">
            <!-- Lewa strona (bez zmian) -->
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

            <!-- Prawa strona -->
            <div class="md:w-1/2 p-8">
                <!-- Nagłówek i cena (bez zmian) -->
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

                <!-- Szczegóły techniczne (bez zmian) -->
                <div class="grid grid-cols-2 gap-4 mb-6 text-sm">
                    <div><span class="block text-gray-500">Typ</span><span class="font-semibold">{{ $car->type->name }}</span></div>
                    <div><span class="block text-gray-500">Przebieg</span><span class="font-semibold">{{ $car->mileage }} km</span></div>
                    <div><span class="block text-gray-500">Lokalizacja</span><span class="font-semibold">{{ $car->branch->city }}</span></div>
                    <div><span class="block text-gray-500">Skrzynia</span><span class="font-semibold">Automatyczna</span></div>
                </div>

                <!-- Formularz Rezerwacji -->
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-100 mb-6">
                    <h3 class="font-bold text-blue-800 mb-3">Rezerwuj termin</h3>
                    
                    @auth
                        @if($car->is_available)
                            <form action="{{ route('rentals.store', $car) }}" method="POST">
                                @csrf
                                <div class="grid grid-cols-2 gap-3 mb-3">
                                    <div>
                                        <label for="start_date" class="block text-xs font-medium text-gray-700 mb-1">Od kiedy</label>
                                        <input type="date" name="start_date" id="start_date" min="{{ date('Y-m-d') }}" required
                                               class="w-full text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label for="end_date" class="block text-xs font-medium text-gray-700 mb-1">Do kiedy</label>
                                        <input type="date" name="end_date" id="end_date" min="{{ date('Y-m-d') }}" required
                                               class="w-full text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="comments" class="block text-xs font-medium text-gray-700 mb-1">Uwagi (opcjonalnie)</label>
                                    <textarea name="comments" id="comments" rows="2" class="w-full text-sm border-gray-300 rounded-md"></textarea>
                                </div>

                                @if($errors->any())
                                    <div class="text-red-600 text-xs mb-3">
                                        {{ $errors->first() }}
                                    </div>
                                @endif

                                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md font-bold hover:bg-blue-700 transition">
                                    Rezerwuj teraz
                                </button>
                            </form>
                        @else
                            <div class="text-center p-4 text-red-600 font-medium">
                                Pojazd jest obecnie wyłączony z użytku.
                            </div>
                        @endif
                    @else
                        <div class="text-center">
                            <p class="text-sm text-gray-600 mb-2">Musisz być zalogowany, aby zarezerwować.</p>
                            <a href="{{ route('login') }}" class="text-blue-600 font-bold hover:underline">Zaloguj się</a>
                        </div>
                    @endauth
                </div>

                <div class="text-center">
                    <a href="{{ route('cars.index') }}" class="text-gray-500 text-sm hover:underline">← Wróć do listy</a>
                </div>
            </div>
        </div>
    </div>
@endsection