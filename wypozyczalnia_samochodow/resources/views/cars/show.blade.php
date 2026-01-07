@extends('layouts.app')

@section('content')
    <div class="max-w-5xl mx-auto bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="md:flex">
            <!-- Lewa strona: Zdjęcie -->
            <div class="md:w-1/2 bg-gray-100 relative min-h-[400px]">
                @if($car->image_path)
                    <img src="{{ asset('storage/' . $car->image_path) }}" alt="{{ $car->brand->name }} {{ $car->model }}" class="w-full h-full object-cover absolute inset-0">
                @else
                    <div class="flex items-center justify-center h-full text-gray-400 flex-col">
                        <span class="text-6xl">🚗</span>
                        <span class="mt-2 text-sm font-medium">Brak zdjęcia</span>
                    </div>
                @endif
                
                @if(!$car->is_available)
                    <div class="absolute top-0 left-0 w-full h-full bg-black bg-opacity-50 flex items-center justify-center">
                        <span class="bg-red-600 text-white px-6 py-2 rounded-full font-bold text-lg shadow-lg">Pojazd Niedostępny</span>
                    </div>
                @endif
            </div>

            <!-- Prawa strona: Informacje -->
            <div class="md:w-1/2 p-8">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <span class="text-sm text-blue-600 font-bold uppercase tracking-wider">{{ $car->brand->name }}</span>
                        <h1 class="text-3xl font-bold text-gray-900 leading-tight">{{ $car->model }}</h1>
                    </div>
                    <div class="text-right bg-blue-50 p-2 rounded-lg">
                        <span class="block text-3xl font-bold text-blue-700">{{ number_format($car->daily_rate, 0) }} zł</span>
                        <span class="text-blue-600 text-xs font-semibold">CENA ZA DOBĘ</span>
                    </div>
                </div>

                <div class="flex items-center gap-2 mb-6">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        {{ $car->type->name }}
                    </span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $car->transmission == 'automatic' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800' }}">
                        {{ $car->transmission == 'automatic' ? 'Automat' : 'Manual' }}
                    </span>
                </div>

                <hr class="my-6 border-gray-100">

                <div class="grid grid-cols-2 gap-y-4 gap-x-8 mb-8 text-sm">
                    <div>
                        <span class="block text-gray-400 text-xs uppercase mb-1">Rocznik</span>
                        <span class="font-semibold text-gray-800 text-lg">{{ $car->year }}</span>
                    </div>
                    <div>
                        <span class="block text-gray-400 text-xs uppercase mb-1">Przebieg</span>
                        <span class="font-semibold text-gray-800 text-lg">{{ number_format($car->mileage, 0, ' ', ' ') }} km</span>
                    </div>
                    <div>
                        <span class="block text-gray-400 text-xs uppercase mb-1">Kolor</span>
                        <span class="font-semibold text-gray-800">{{ ucfirst($car->color) }}</span>
                    </div>
                    <div>
                        <span class="block text-gray-400 text-xs uppercase mb-1">Numer Rej.</span>
                        <span class="font-semibold text-gray-800">{{ $car->registration_plate }}</span>
                    </div>
                </div>

                <!-- Lokalizacja -->
                <div class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-100">
                    <h3 class="text-xs font-bold text-gray-500 uppercase mb-2">Lokalizacja odbioru</h3>
                    <p class="text-gray-800 font-semibold">{{ $car->branch->name }}</p>
                    <p class="text-sm text-gray-600">{{ $car->branch->address }}, {{ $car->branch->city }}</p>
                    <p class="text-sm text-gray-600 mt-1">📞 {{ $car->branch->phone }}</p>
                </div>

                <!-- Wyposażenie -->
                <div class="mb-8">
                    <h3 class="text-xs font-bold text-gray-500 uppercase mb-3">Wyposażenie</h3>
                    <div class="flex flex-wrap gap-2">
                        @forelse($car->features as $feature)
                            <span class="px-3 py-1 bg-white text-gray-700 rounded-full text-xs font-medium border border-gray-300 shadow-sm flex items-center gap-1">
                                <span>✅</span> {{ $feature->name }}
                            </span>
                        @empty
                            <span class="text-gray-400 text-sm italic">Brak dodatkowego wyposażenia</span>
                        @endforelse
                    </div>
                </div>

                <!-- Formularz Rezerwacji -->
                <div class="bg-blue-600 p-6 rounded-xl shadow-lg text-white">
                    <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
                        <span>📅</span> Zarezerwuj ten samochód
                    </h3>
                    
                    @auth
                        @if($car->is_available)
                            <form action="{{ route('rentals.store', $car) }}" method="POST">
                                @csrf
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label for="start_date" class="block text-xs font-medium text-blue-100 mb-1">Od kiedy</label>
                                        <input type="date" name="start_date" id="start_date" min="{{ date('Y-m-d') }}" required 
                                               class="w-full text-sm border-0 rounded bg-white text-gray-900 focus:ring-2 focus:ring-blue-300">
                                    </div>
                                    <div>
                                        <label for="end_date" class="block text-xs font-medium text-blue-100 mb-1">Do kiedy</label>
                                        <input type="date" name="end_date" id="end_date" min="{{ date('Y-m-d') }}" required 
                                               class="w-full text-sm border-0 rounded bg-white text-gray-900 focus:ring-2 focus:ring-blue-300">
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label for="comments" class="block text-xs font-medium text-blue-100 mb-1">Uwagi do rezerwacji</label>
                                    <textarea name="comments" id="comments" rows="2" class="w-full text-sm border-0 rounded bg-white text-gray-900 focus:ring-2 focus:ring-blue-300" placeholder="Np. proszę o fotelik dziecięcy..."></textarea>
                                </div>

                                @if($errors->any())
                                    <div class="bg-red-500 text-white text-xs p-2 rounded mb-3">
                                        {{ $errors->first() }}
                                    </div>
                                @endif

                                <button type="submit" class="w-full bg-white text-blue-600 py-3 rounded-lg font-bold hover:bg-gray-100 transition shadow-md">
                                    Potwierdź rezerwację
                                </button>
                            </form>
                        @else
                            <div class="text-center p-4 bg-red-500 rounded-lg">
                                <p class="font-bold">Samochód tymczasowo wyłączony z floty.</p>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-2">
                            <p class="text-sm text-blue-100 mb-3">Zaloguj się, aby dokonać rezerwacji.</p>
                            <a href="{{ route('login') }}" class="inline-block bg-white text-blue-600 px-6 py-2 rounded-lg font-bold hover:bg-gray-100 transition">
                                Przejdź do logowania
                            </a>
                        </div>
                    @endauth
                </div>

                <div class="text-center mt-6">
                    <a href="{{ route('cars.index') }}" class="text-gray-500 text-sm hover:text-gray-800 transition font-medium">← Wróć do wyszukiwarki</a>
                </div>
            </div>
        </div>
    </div>
@endsection