@extends('layouts.app')

@section('content')
    <div class="max-w-5xl mx-auto bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
        <div class="md:flex">
            <!-- Lewa strona: Zdjęcie -->
            <div class="md:w-1/2 bg-gray-100 relative min-h-[400px]">
                @if($car->image_path)
                    <img src="{{ asset('storage/' . $car->image_path) }}" 
                         alt="Zdjęcie samochodu {{ $car->brand->name }} {{ $car->model }}, kolor {{ $car->color }}" 
                         class="w-full h-full object-cover absolute inset-0">
                @else
                    <div class="flex items-center justify-center h-full text-gray-500 flex-col" aria-hidden="true">
                        <span class="text-6xl">🚗</span>
                        <span class="mt-2 text-sm font-medium">Brak zdjęcia poglądowego</span>
                    </div>
                    <span class="sr-only">Brak zdjęcia dla tego samochodu.</span>
                @endif
                
                @if(!$car->is_available)
                    <!-- Ostrzeżenie dla czytnika (role="alert") -->
                    <div class="absolute top-0 left-0 w-full h-full bg-black bg-opacity-60 flex items-center justify-center" role="alert">
                        <span class="bg-red-700 text-white px-6 py-2 rounded-full font-bold text-lg shadow-lg border-2 border-white">
                            Pojazd Niedostępny
                        </span>
                    </div>
                @endif
            </div>

            <!-- Prawa strona: Informacje -->
            <div class="md:w-1/2 p-8 flex flex-col">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <span class="text-sm text-blue-700 font-bold uppercase tracking-wider block">{{ $car->brand->name }}</span>
                        <h1 class="text-3xl font-bold text-gray-900 leading-tight">{{ $car->model }}</h1>
                    </div>
                    <div class="text-right bg-blue-50 p-3 rounded-lg border border-blue-100">
                        <span class="block text-3xl font-bold text-blue-800" id="daily-rate" data-rate="{{ $car->daily_rate }}">
                            <span class="sr-only">Cena: </span>{{ number_format($car->daily_rate, 0) }} zł
                        </span>
                        <span class="text-blue-800 text-xs font-bold uppercase" aria-hidden="true">Cena za dobę</span>
                        <span class="sr-only">za dobę</span>
                    </div>
                </div>

                <!-- Średnia ocena w szczegółach -->
                    <div class="flex items-center gap-2 mb-4">
                        <div class="flex text-yellow-400 text-lg" aria-hidden="true">
                            @php $rating = $car->reviews?->avg('rating') ?? 0; @endphp
                            @for($i=1; $i<=5; $i++)
                                <span>{{ $i <= round($rating) ? '★' : '☆' }}</span>
                            @endfor
                        </div>
                        <span class="text-sm text-gray-600 font-medium">
                            @if($car->reviews && $car->reviews->count() > 0)
                                {{ number_format($rating, 1) }} / 5 ({{ $car->reviews->count() }} opinii)
                            @else
                                Brak opinii
                            @endif
                        </span>
                    </div>

                <div class="flex flex-wrap items-center gap-2 mb-6" aria-label="Główne cechy pojazdu">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800 border border-gray-300">
                        <span class="sr-only">Typ nadwozia: </span>{{ $car->type->name }}
                    </span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border {{ $car->transmission == 'automatic' ? 'bg-purple-100 text-purple-900 border-purple-200' : 'bg-green-100 text-green-900 border-green-200' }}">
                        <span class="sr-only">Skrzynia biegów: </span>{{ $car->transmission == 'automatic' ? 'Automatyczna' : 'Manualna' }}
                    </span>
                </div>

                <hr class="my-6 border-gray-200" aria-hidden="true">

                <!-- Szczegóły techniczne z opisami dla czytnika -->
                <div class="grid grid-cols-2 gap-y-4 gap-x-8 mb-6 text-sm" role="list" aria-label="Parametry techniczne">
                    <div role="listitem">
                        <span class="block text-gray-500 text-xs uppercase font-bold mb-1" aria-hidden="true">Rocznik</span>
                        <span class="font-semibold text-gray-900 text-lg">
                            <span class="sr-only">Rok produkcji: </span>{{ $car->year }}
                        </span>
                    </div>
                    <div role="listitem">
                        <span class="block text-gray-500 text-xs uppercase font-bold mb-1" aria-hidden="true">Przebieg</span>
                        <span class="font-semibold text-gray-900 text-lg">
                            <span class="sr-only">Przebieg: </span>{{ number_format($car->mileage, 0, ' ', ' ') }} km
                        </span>
                    </div>
                    <div role="listitem">
                        <span class="block text-gray-500 text-xs uppercase font-bold mb-1" aria-hidden="true">Kolor</span>
                        <span class="font-semibold text-gray-900">
                            <span class="sr-only">Kolor: </span>{{ ucfirst($car->color) }}
                        </span>
                    </div>
                    <div role="listitem">
                        <span class="block text-gray-500 text-xs uppercase font-bold mb-1" aria-hidden="true">Lokalizacja</span>
                        <span class="font-semibold text-gray-900">
                            <span class="sr-only">Lokalizacja odbioru: </span>{{ $car->branch->city }}
                        </span>
                    </div>
                </div>

                <!-- Wyposażenie -->
                <div class="mb-8">
                    <h2 class="text-xs font-bold text-gray-500 uppercase mb-3">Wyposażenie</h2>
                    <ul class="flex flex-wrap gap-2" aria-label="Lista wyposażenia dodatkowego">
                        @forelse($car->features as $feature)
                            <li class="px-3 py-1 bg-white text-gray-700 rounded-full text-xs font-medium border border-gray-300 shadow-sm flex items-center gap-1">
                                <span aria-hidden="true">✅</span> {{ $feature->name }}
                            </li>
                        @empty
                            <li class="text-gray-500 text-sm italic">Brak dodatkowego wyposażenia</li>
                        @endforelse
                    </ul>
                </div>

                <!-- Formularz Rezerwacji -->
                <div class="bg-blue-700 p-6 rounded-xl shadow-lg text-white mt-auto">
                    <h2 class="font-bold text-xl mb-4 flex items-center gap-2 text-white">
                        <span aria-hidden="true">📅</span> Kalkulator Rezerwacji
                    </h2>
                    
                    @auth
                        @if($car->is_available)
                            <form action="{{ route('rentals.store', $car) }}" method="POST" id="rental-form">
                                @csrf
                                
                                <!-- Daty -->
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label for="start_date" class="block text-sm font-bold text-blue-50 mb-1">Odbiór ({{ $car->branch->city }})</label>
                                        <input type="date" name="start_date" id="start_date" min="{{ date('Y-m-d') }}" required 
                                               aria-required="true"
                                               class="w-full text-sm border-0 rounded p-2 text-gray-900 focus:ring-2 focus:ring-yellow-400 font-medium">
                                    </div>
                                    <div>
                                        <label for="end_date" class="block text-sm font-bold text-blue-50 mb-1">Zwrot</label>
                                        <input type="date" name="end_date" id="end_date" min="{{ date('Y-m-d') }}" required 
                                               aria-required="true"
                                               class="w-full text-sm border-0 rounded p-2 text-gray-900 focus:ring-2 focus:ring-yellow-400 font-medium">
                                    </div>
                                </div>

                                <!-- Lokalizacja zwrotu -->
                                <fieldset class="mb-4 bg-blue-800 p-3 rounded-lg border border-blue-600">
                                    <legend class="sr-only">Opcje zwrotu</legend>
                                    <div class="flex items-center mb-2">
                                        <input type="checkbox" id="diff_location" name="diff_location" class="w-5 h-5 text-blue-600 bg-white border-gray-300 rounded focus:ring-yellow-400 focus:ring-offset-blue-800">
                                        <label for="diff_location" class="ml-2 text-sm font-bold text-white cursor-pointer">Zwrot w innej lokalizacji (+100 zł)</label>
                                    </div>
                                    
                                    <div id="location-select-wrapper" class="hidden transition-opacity duration-300">
                                        <label for="destination_branch_id" class="block text-xs font-bold text-blue-200 mb-1">Wybierz oddział zwrotu</label>
                                        <select name="destination_branch_id" id="destination_branch_id" class="w-full text-sm border-0 rounded bg-blue-900 text-white focus:ring-2 focus:ring-yellow-400 p-2">
                                            @foreach($branches as $branch)
                                                <option value="{{ $branch->id }}" {{ $branch->id == $car->branch_id ? 'selected' : '' }}>
                                                    {{ $branch->city }} ({{ $branch->name }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </fieldset>

                                <!-- Podsumowanie Ceny - Live Region -->
                                <div class="flex justify-between items-center mb-4 border-t border-blue-500 pt-3" aria-live="polite" aria-atomic="true">
                                    <div class="text-sm text-blue-100">
                                        Liczba dni: <span id="days-count" class="font-bold">0</span> <br>
                                        <span id="location-fee" class="hidden text-xs text-yellow-300 font-bold block mt-1">+ opłata relokacyjna 100 zł</span>
                                    </div>
                                    <div class="text-right">
                                        <span class="block text-xs text-blue-200 uppercase font-bold" aria-hidden="true">Przewidywany koszt</span>
                                        <span class="sr-only">Przewidywany koszt całkowity: </span>
                                        <span class="text-3xl font-extrabold text-white" id="total-price">0.00 zł</span>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="comments" class="block text-sm font-bold text-blue-50 mb-1">Uwagi (opcjonalne)</label>
                                    <textarea name="comments" id="comments" rows="2" class="w-full text-sm border-0 rounded bg-white text-gray-900 focus:ring-2 focus:ring-yellow-400 p-2" placeholder="Np. proszę o fotelik dziecięcy..."></textarea>
                                </div>

                                @if($errors->any())
                                    <div class="bg-red-600 text-white text-sm p-3 rounded mb-3 border border-red-400" role="alert">
                                        <strong>Uwaga:</strong> {{ $errors->first() }}
                                    </div>
                                @endif

                                <button type="submit" class="w-full bg-white text-blue-800 py-3 rounded-lg font-extrabold hover:bg-gray-100 transition shadow-md focus:ring-2 focus:ring-offset-2 focus:ring-offset-blue-800 focus:ring-white">
                                    Potwierdź rezerwację
                                </button>
                            </form>
                        @else
                            <div class="text-center p-4 bg-red-600 rounded-lg border border-red-400" role="alert">
                                <p class="font-bold text-white">Samochód tymczasowo wyłączony z floty.</p>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <p class="text-sm text-blue-100 mb-3 font-medium">Zaloguj się, aby dokonać rezerwacji.</p>
                            <a href="{{ route('login') }}" class="inline-block bg-white text-blue-700 px-6 py-2 rounded-lg font-bold hover:bg-gray-100 transition focus:ring-2 focus:ring-offset-2 focus:ring-offset-blue-700 focus:ring-white">
                                Przejdź do logowania
                            </a>
                        </div>
                    @endauth
                </div>

                <div class="text-center mt-6">
                    <a href="{{ route('cars.index') }}" class="text-gray-600 text-sm hover:text-gray-900 transition font-bold underline focus:text-blue-700">← Wróć do wyszukiwarki</a>
                </div>
            </div>
        </div>
    </div>

    <!-- SEKJA OPINII (NOWOŚĆ) -->
        <section class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden" aria-labelledby="reviews-heading">
            <div class="p-6 border-b border-gray-200 bg-gray-50">
                <h2 id="reviews-heading" class="text-xl font-bold text-gray-800">Opinie klientów ({{ $car->reviews->count() }})</h2>
            </div>
            
            <div class="divide-y divide-gray-200">
                @forelse($car->reviews as $review)
                    <article class="p-6 hover:bg-gray-50 transition">
                        <div class="flex justify-between items-start">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-lg" aria-hidden="true">
                                    {{ substr($review->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900">{{ $review->user->name }}</h3>
                                    <time datetime="{{ $review->created_at->format('Y-m-d') }}" class="text-xs text-gray-500">
                                        {{ $review->created_at->format('d.m.Y') }}
                                    </time>
                                </div>
                            </div>
                            
                            <div class="flex flex-col items-end">
                                <div class="flex text-yellow-400 text-sm" aria-label="Ocena: {{ $review->rating }} na 5">
                                    @for($i=1; $i<=5; $i++)
                                        <span aria-hidden="true">{{ $i <= $review->rating ? '★' : '☆' }}</span>
                                    @endfor
                                </div>
                                
                                {{-- Przycisk Usuwania dla Admina --}}
                                @if(Auth::check() && Auth::user()->role === 'admin')
                                    <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" class="mt-2" onsubmit="return confirm('Usunąć tę opinię?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-bold underline focus:outline-none focus:ring-2 focus:ring-red-500 rounded px-1">
                                            Usuń opinię
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                        
                        <p class="mt-3 text-gray-700 text-sm leading-relaxed">
                            {{ $review->comment }}
                        </p>
                    </article>
                @empty
                    <div class="p-10 text-center text-gray-500">
                        <p class="text-lg">Ten samochód nie ma jeszcze żadnych opinii.</p>
                    </div>
                @endforelse
            </div>
        </section>

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
                        locSelectWrapper.classList.remove('hidden');
                    } else {
                        locFeeEl.classList.add('hidden');
                        locSelectWrapper.classList.add('hidden');
                    }

                    daysCountEl.innerText = days;
                    totalPriceEl.innerText = total.toFixed(2) + ' zł';
                } else {
                    daysCountEl.innerText = '0';
                    totalPriceEl.innerText = '0.00 zł';
                }
            }

            // Event Listenery
            if(startDateInput) startDateInput.addEventListener('change', calculatePrice);
            if(endDateInput) endDateInput.addEventListener('change', calculatePrice);
            if(diffLocCheckbox) diffLocCheckbox.addEventListener('change', calculatePrice);
        });
    </script>
@endsection