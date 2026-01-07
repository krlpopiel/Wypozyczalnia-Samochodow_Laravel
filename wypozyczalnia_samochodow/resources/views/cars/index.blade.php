@extends('layouts.app')

@section('content')
    <h1 class="sr-only">Lista dostępnych samochodów</h1> <!-- WCAG: H1 na każdej stronie -->

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        
        <!-- Panel boczny: Filtry -->
        <aside class="md:col-span-1" aria-label="Filtry wyszukiwania">
            <div class="bg-white p-4 rounded-lg shadow sticky top-4 border border-gray-200">
                <h2 class="text-lg font-bold mb-4 border-b pb-2 text-gray-800">Filtrowanie</h2>
                <form action="{{ route('cars.index') }}" method="GET">
                    
                    <!-- Dostępność (Daty) -->
                    <fieldset class="mb-4 bg-blue-50 p-3 rounded-md border border-blue-200">
                        <legend class="text-sm font-bold text-blue-900 mb-2 uppercase w-full">Termin wynajmu</legend>
                        <div class="mb-2">
                            <label for="start_date" class="block text-sm font-semibold text-gray-800 mb-1">Od kiedy</label>
                            <input type="date" name="start_date" id="start_date" 
                                   value="{{ request('start_date') }}" min="{{ date('Y-m-d') }}"
                                   class="w-full text-sm border-gray-400 rounded focus:border-blue-600 focus:ring-blue-200 text-gray-900">
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-semibold text-gray-800 mb-1">Do kiedy</label>
                            <input type="date" name="end_date" id="end_date" 
                                   value="{{ request('end_date') }}" min="{{ date('Y-m-d') }}"
                                   class="w-full text-sm border-gray-400 rounded focus:border-blue-600 focus:ring-blue-200 text-gray-900">
                        </div>
                    </fieldset>

                    <!-- Oddział -->
                    <div class="mb-4">
                        <label for="branch" class="block text-sm font-semibold text-gray-800 mb-1">Oddział / Miasto</label>
                        <select name="branch" id="branch" class="w-full text-sm border-gray-400 rounded-md shadow-sm text-gray-900 focus:border-blue-600 focus:ring-blue-200">
                            <option value="">Wszystkie lokalizacje</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ request('branch') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->city }} ({{ $branch->name }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Marka -->
                    <div class="mb-4">
                        <label for="brand" class="block text-sm font-semibold text-gray-800 mb-1">Marka</label>
                        <select name="brand" id="brand" class="w-full text-sm border-gray-400 rounded-md shadow-sm text-gray-900 focus:border-blue-600 focus:ring-blue-200">
                            <option value="">Wszystkie marki</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ request('brand') == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Typ -->
                    <div class="mb-4">
                        <label for="type" class="block text-sm font-semibold text-gray-800 mb-1">Klasa pojazdu</label>
                        <select name="type" id="type" class="w-full text-sm border-gray-400 rounded-md shadow-sm text-gray-900 focus:border-blue-600 focus:ring-blue-200">
                            <option value="">Wszystkie</option>
                            @foreach($types as $type)
                                <option value="{{ $type->id }}" {{ request('type') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Skrzynia Biegów -->
                    <div class="mb-4">
                        <label for="transmission" class="block text-sm font-semibold text-gray-800 mb-1">Skrzynia biegów</label>
                        <select name="transmission" id="transmission" class="w-full text-sm border-gray-400 rounded-md shadow-sm text-gray-900 focus:border-blue-600 focus:ring-blue-200">
                            <option value="">Dowolna</option>
                            <option value="manual" {{ request('transmission') == 'manual' ? 'selected' : '' }}>Manualna</option>
                            <option value="automatic" {{ request('transmission') == 'automatic' ? 'selected' : '' }}>Automatyczna</option>
                        </select>
                    </div>

                    <!-- Cena -->
                    <fieldset class="mb-4">
                        <legend class="block text-sm font-semibold text-gray-800 mb-1">Cena za dobę (zł)</legend>
                        <div class="flex gap-2">
                            <div class="w-1/2">
                                <label for="min_price" class="sr-only">Cena od</label>
                                <input type="number" name="min_price" id="min_price" placeholder="Od" value="{{ request('min_price') }}" 
                                       class="w-full text-sm border-gray-400 rounded text-gray-900 focus:border-blue-600 focus:ring-blue-200">
                            </div>
                            <div class="w-1/2">
                                <label for="max_price" class="sr-only">Cena do</label>
                                <input type="number" name="max_price" id="max_price" placeholder="Do" value="{{ request('max_price') }}" 
                                       class="w-full text-sm border-gray-400 rounded text-gray-900 focus:border-blue-600 focus:ring-blue-200">
                            </div>
                        </div>
                    </fieldset>

                    <!-- Rocznik -->
                    <fieldset class="mb-4">
                        <legend class="block text-sm font-semibold text-gray-800 mb-1">Rocznik</legend>
                        <div class="flex gap-2">
                            <div class="w-1/2">
                                <label for="year_from" class="sr-only">Rocznik od</label>
                                <input type="number" name="year_from" id="year_from" placeholder="Od" value="{{ request('year_from') }}" 
                                       class="w-full text-sm border-gray-400 rounded text-gray-900 focus:border-blue-600 focus:ring-blue-200">
                            </div>
                            <div class="w-1/2">
                                <label for="year_to" class="sr-only">Rocznik do</label>
                                <input type="number" name="year_to" id="year_to" placeholder="Do" value="{{ request('year_to') }}" 
                                       class="w-full text-sm border-gray-400 rounded text-gray-900 focus:border-blue-600 focus:ring-blue-200">
                            </div>
                        </div>
                    </fieldset>

                    <!-- Wyposażenie -->
                    <fieldset class="mb-6 border-t border-gray-300 pt-3">
                        <legend class="block text-sm font-semibold text-gray-800 mb-2">Wyposażenie (zawiera)</legend>
                        <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                            @foreach($features as $feature)
                                <div class="flex items-center">
                                    <input type="checkbox" name="features[]" value="{{ $feature->id }}" id="f_{{ $feature->id }}"
                                           class="h-4 w-4 text-blue-600 border-gray-400 rounded focus:ring-blue-600"
                                           {{ in_array($feature->id, request('features', [])) ? 'checked' : '' }}>
                                    <label for="f_{{ $feature->id }}" class="ml-2 text-sm text-gray-700">{{ $feature->name }}</label>
                                </div>
                            @endforeach
                        </div>
                    </fieldset>

                    <button type="submit" class="w-full bg-blue-700 text-white py-2 px-4 rounded-md hover:bg-blue-800 transition shadow font-bold text-sm focus:ring-2 focus:ring-offset-2 focus:ring-blue-700">
                        Filtruj wyniki
                    </button>
                    
                    <a href="{{ route('cars.index') }}" class="block text-center mt-3 text-sm text-gray-600 hover:text-gray-900 underline focus:text-blue-700">
                        Wyczyść wszystkie filtry
                    </a>
                </form>
            </div>
        </aside>

        <!-- Lista samochodów -->
        <div class="md:col-span-3">
            @if(request('start_date') || request('branch') || request('transmission'))
                <div class="mb-4 p-4 bg-gray-50 rounded border border-gray-300 text-sm text-gray-800 flex flex-wrap gap-3 items-center" role="status">
                    <span class="font-bold">Aktywne filtry:</span>
                    @if(request('start_date')) <span class="bg-blue-100 text-blue-900 px-3 py-1 rounded-full font-medium border border-blue-200">📅 {{ request('start_date') }} - {{ request('end_date') }}</span> @endif
                    @if(request('branch')) <span class="bg-blue-100 text-blue-900 px-3 py-1 rounded-full font-medium border border-blue-200">🏢 Wybrany oddział</span> @endif
                    @if(request('transmission')) <span class="bg-blue-100 text-blue-900 px-3 py-1 rounded-full font-medium border border-blue-200">⚙️ {{ request('transmission') == 'automatic' ? 'Automat' : 'Manual' }}</span> @endif
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($cars as $car)
                    <article class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden flex flex-col h-full transition hover:shadow-xl group focus-within:ring-4 focus-within:ring-blue-300">
                        <div class="h-48 bg-gray-200 flex items-center justify-center text-gray-500 overflow-hidden relative border-b border-gray-200">
                            @if($car->image_path)
                                <img src="{{ asset('storage/' . $car->image_path) }}" 
                                     alt="Samochód {{ $car->brand->name }} {{ $car->model }}, kolor {{ $car->color }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="text-center">
                                    <span class="text-4xl block" aria-hidden="true">🚗</span>
                                    <span class="text-xs mt-2 font-medium">Brak zdjęcia</span>
                                </div>
                            @endif
                            
                            @if(!$car->is_available)
                                <span class="absolute top-2 right-2 bg-red-700 text-white text-xs font-bold px-3 py-1 rounded-full shadow">Niedostępny</span>
                            @endif
                        </div>

                        <div class="p-4 flex-grow">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="text-xl font-bold text-gray-900 leading-tight">
                                    <a href="{{ route('cars.show', $car) }}" class="hover:text-blue-800 focus:outline-none focus:underline">
                                        {{ $car->brand->name }} {{ $car->model }}
                                    </a>
                                </h3>
                            </div>
                            
                            <div class="text-sm text-gray-700 mb-4 space-y-1">
                                <p class="flex items-center gap-2">
                                    <span aria-hidden="true">📅</span> 
                                    <span class="sr-only">Rocznik:</span> {{ $car->year }} • 
                                    <span class="sr-only">Skrzynia biegów:</span> {{ $car->transmission == 'automatic' ? 'Automat' : 'Manual' }}
                                </p>
                                <p class="flex items-center gap-2">
                                    <span aria-hidden="true">📍</span> 
                                    <span class="sr-only">Lokalizacja:</span> {{ $car->branch->city }}
                                </p>
                            </div>

                            <div class="flex flex-wrap gap-2 mb-2">
                                @foreach($car->features->take(3) as $feature)
                                    <span class="inline-block bg-gray-100 text-gray-800 text-xs font-semibold px-2 py-1 rounded border border-gray-300">
                                        {{ $feature->name }}
                                    </span>
                                @endforeach
                                @if($car->features->count() > 3)
                                    <span class="text-xs text-gray-600 self-center font-medium">+{{ $car->features->count() - 3 }} więcej</span>
                                @endif
                            </div>
                        </div>

                        <div class="p-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                            <div>
                                <span class="text-2xl font-bold text-blue-800">{{ number_format($car->daily_rate, 0) }} zł</span>
                                <span class="text-xs text-gray-600 block font-medium">/ dzień</span>
                            </div>
                            <a href="{{ route('cars.show', $car) }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-700 border border-transparent rounded-md font-bold text-sm text-white uppercase tracking-widest hover:bg-blue-800 focus:bg-blue-800 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition"
                               aria-label="Zobacz szczegóły i wynajmij {{ $car->brand->name }} {{ $car->model }}">
                                Szczegóły
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="col-span-3 p-12 text-center bg-white rounded-lg shadow-sm border border-dashed border-gray-300">
                        <div class="text-5xl mb-4" aria-hidden="true">🔍</div>
                        <h3 class="text-lg font-bold text-gray-900">Brak samochodów</h3>
                        <p class="text-gray-600 mt-2">
                            @if(request('start_date'))
                                Wszystkie auta są zajęte w wybranym terminie. Spróbuj zmienić daty.
                            @else
                                Nie znaleziono pojazdów spełniających Twoje kryteria.
                            @endif
                        </p>
                        <a href="{{ route('cars.index') }}" class="mt-4 inline-block text-blue-700 font-bold hover:underline focus:text-blue-900">Wyczyść wszystkie filtry</a>
                    </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $cars->links() }}
            </div>
        </div>
    </div>
@endsection