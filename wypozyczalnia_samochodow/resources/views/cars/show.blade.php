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
            <div class="md:w-1/2 p-8 flex flex-col">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <span class="text-sm text-blue-600 font-bold uppercase tracking-wider">{{ $car->brand->name }}</span>
                        <h1 class="text-3xl font-bold text-gray-900 leading-tight">{{ $car->model }}</h1>
                    </div>
                    <div class="text-right bg-blue-50 p-2 rounded-lg">
                        <span class="block text-3xl font-bold text-blue-700" id="daily-rate" data-rate="{{ $car->daily_rate }}">{{ number_format($car->daily_rate, 0) }} zł</span>
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

                <!-- Szczegóły techniczne -->
                <div class="grid grid-cols-2 gap-y-4 gap-x-8 mb-6 text-sm">
                    <div><span class="block text-gray-400 text-xs uppercase mb-1">Rocznik</span><span class="font-semibold text-gray-800">{{ $car->year }}</span></div>
                    <div><span class="block text-gray-400 text-xs uppercase mb-1">Przebieg</span><span class="font-semibold text-gray-800">{{ number_format($car->mileage, 0, ' ', ' ') }} km</span></div>
                    <div><span class="block text-gray-400 text-xs uppercase mb-1">Kolor</span><span class="font-semibold text-gray-800">{{ ucfirst($car->color) }}</span></div>
                    <div><span class="block text-gray-400 text-xs uppercase mb-1">Lokalizacja</span><span class="font-semibold text-gray-800">{{ $car->branch->city }}</span></div>
                </div>

                <!-- Formularz Rezerwacji -->
                <div class="bg-blue-600 p-6 rounded-xl shadow-lg text-white mt-auto">
                    <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
                        <span>📅</span> Kalkulator Rezerwacji
                    </h3>
                    
                    @auth
                        @if($car->is_available)
                            <form action="{{ route('rentals.store', $car) }}" method="POST" id="rental-form">
                                @csrf
                                
                                <!-- Daty -->
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label for="start_date" class="block text-xs font-medium text-blue-100 mb-1">Odbiór ({{ $car->branch->city }})</label>
                                        <input type="date" name="start_date" id="start_date" min="{{ date('Y-m-d') }}" required 
                                               class="w-full text-sm border-0 rounded bg-white text-gray-900 focus:ring-2 focus:ring-blue-300">
                                    </div>
                                    <div>
                                        <label for="end_date" class="block text-xs font-medium text-blue-100 mb-1">Zwrot</label>
                                        <input type="date" name="end_date" id="end_date" min="{{ date('Y-m-d') }}" required 
                                               class="w-full text-sm border-0 rounded bg-white text-gray-900 focus:ring-2 focus:ring-blue-300">
                                    </div>
                                </div>

                                <!-- Lokalizacja zwrotu -->
                                <div class="mb-4 bg-blue-700 p-3 rounded-lg border border-blue-500">
                                    <div class="flex items-center mb-2">
                                        <input type="checkbox" id="diff_location" name="diff_location" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                        <label for="diff_location" class="ml-2 text-sm font-medium text-blue-100">Zwrot w innej lokalizacji (+100 zł)</label>
                                    </div>
                                    
                                    <div id="location-select-wrapper" class="hidden opacity-50 pointer-events-none transition-opacity duration-300">
                                        <label for="destination_branch_id" class="block text-xs font-medium text-blue-200 mb-1">Wybierz oddział zwrotu</label>
                                        <select name="destination_branch_id" id="destination_branch_id" class="w-full text-sm border-0 rounded bg-blue-800 text-white focus:ring-2 focus:ring-blue-300">
                                            @foreach($branches as $branch)
                                                <option value="{{ $branch->id }}" {{ $branch->id == $car->branch_id ? 'selected' : '' }}>
                                                    {{ $branch->city }} ({{ $branch->name }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Podsumowanie Ceny -->
                                <div class="flex justify-between items-center mb-4 border-t border-blue-500 pt-3">
                                    <div class="text-sm text-blue-200">
                                        <span id="days-count">0</span> dni <br>
                                        <span id="location-fee" class="hidden text-xs text-yellow-300">+ opłata relokacyjna</span>
                                    </div>
                                    <div class="text-right">
                                        <span class="block text-xs text-blue-200">Przewidywany koszt:</span>
                                        <span class="text-2xl font-bold" id="total-price">0.00 zł</span>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="comments" class="block text-xs font-medium text-blue-100 mb-1">Uwagi</label>
                                    <textarea name="comments" id="comments" rows="1" class="w-full text-sm border-0 rounded bg-white text-gray-900 focus:ring-2 focus:ring-blue-300"></textarea>
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
                        <div class="text-center py-4">
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

    <!-- Skrypt do dynamicznej wyceny -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            const diffLocCheckbox = document.getElementById('diff_location');
            const locSelectWrapper = document.getElementById('location-select-wrapper');
            const dailyRate = parseFloat(document.getElementById('daily-rate').dataset.rate);
            
            const totalPriceEl = document.getElementById('total-price');
            const daysCountEl = document.getElementById('days-count');
            const locFeeEl = document.getElementById('location-fee');

            function calculatePrice() {
                const start = new Date(startDateInput.value);
                const end = new Date(endDateInput.value);
                
                // Walidacja dat
                if (startDateInput.value && endDateInput.value && end >= start) {
                    const diffTime = Math.abs(end - start);
                    let days = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                    if (days === 0) days = 1; // Minimum 1 dzień

                    let total = days * dailyRate;
                    
                    // Opłata za inną lokalizację
                    if (diffLocCheckbox.checked) {
                        total += 100;
                        locFeeEl.classList.remove('hidden');
                        locSelectWrapper.classList.remove('hidden', 'opacity-50', 'pointer-events-none');
                    } else {
                        locFeeEl.classList.add('hidden');
                        locSelectWrapper.classList.add('hidden', 'opacity-50', 'pointer-events-none');
                    }

                    daysCountEl.innerText = days;
                    totalPriceEl.innerText = total.toFixed(2) + ' zł';
                } else {
                    daysCountEl.innerText = '0';
                    totalPriceEl.innerText = '0.00 zł';
                }
            }

            // Event Listenery
            startDateInput.addEventListener('change', calculatePrice);
            endDateInput.addEventListener('change', calculatePrice);
            diffLocCheckbox.addEventListener('change', calculatePrice);
        });
    </script>
@endsection