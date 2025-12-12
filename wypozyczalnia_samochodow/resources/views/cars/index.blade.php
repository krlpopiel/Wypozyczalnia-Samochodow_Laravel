@extends('layouts.app')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        
        <!-- Panel boczny: Filtry -->
        <aside class="md:col-span-1">
            <div class="bg-white p-4 rounded-lg shadow sticky top-4">
                <h2 class="text-lg font-semibold mb-4 border-b pb-2">Znajdź samochód</h2>
                <form action="{{ route('cars.index') }}" method="GET">
                    
                    <!-- NOWE: Filtr Daty -->
                    <div class="mb-4 bg-blue-50 p-3 rounded-md border border-blue-100">
                        <p class="text-xs font-bold text-blue-800 mb-2 uppercase">Dostępność terminowa</p>
                        
                        <div class="mb-2">
                            <label for="start_date" class="block text-xs font-medium text-gray-700 mb-1">Od kiedy</label>
                            <input type="date" name="start_date" id="start_date" 
                                   value="{{ request('start_date') }}"
                                   min="{{ date('Y-m-d') }}"
                                   class="w-full text-sm border-gray-300 rounded-md focus:border-blue-500 focus:ring-blue-200">
                        </div>
                        
                        <div class="mb-2">
                            <label for="end_date" class="block text-xs font-medium text-gray-700 mb-1">Do kiedy</label>
                            <input type="date" name="end_date" id="end_date" 
                                   value="{{ request('end_date') }}"
                                   min="{{ date('Y-m-d') }}"
                                   class="w-full text-sm border-gray-300 rounded-md focus:border-blue-500 focus:ring-blue-200">
                        </div>
                    </div>

                    <!-- Filtr Marki -->
                    <div class="mb-4">
                        <label for="brand" class="block text-sm font-medium text-gray-700 mb-1">Marka</label>
                        <select name="brand" id="brand" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                            <option value="">Wszystkie</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ request('brand') == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filtr Typu -->
                    <div class="mb-4">
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Typ nadwozia</label>
                        <select name="type" id="type" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                            <option value="">Wszystkie</option>
                            @foreach($types as $type)
                                <option value="{{ $type->id }}" {{ request('type') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filtr Ceny -->
                    <div class="mb-4">
                        <label for="max_price" class="block text-sm font-medium text-gray-700 mb-1">Cena max (zł/dzień)</label>
                        <input type="number" name="max_price" id="max_price" 
                               value="{{ request('max_price') }}" 
                               class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                               placeholder="np. 300">
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition shadow">
                        Szukaj dostępnych
                    </button>
                    
                    <a href="{{ route('cars.index') }}" class="block text-center mt-3 text-sm text-gray-600 hover:text-gray-900 underline">
                        Wyczyść filtry
                    </a>
                </form>
            </div>
        </aside>

        <!-- Lista samochodów -->
        <div class="md:col-span-3">
            @if(request('start_date'))
                <div class="mb-4 p-3 bg-blue-50 text-blue-800 rounded border border-blue-200 flex items-center">
                    <span class="text-xl mr-2">📅</span>
                    <div>
                        <span class="font-bold">Wybrano termin:</span> 
                        {{ request('start_date') }} - {{ request('end_date') }}
                        <div class="text-xs mt-1 text-blue-600">Poniższa lista zawiera tylko pojazdy wolne w tym terminie.</div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($cars as $car)
                    <article class="bg-white rounded-lg shadow overflow-hidden flex flex-col h-full transition hover:shadow-lg group">
                        <!-- Obrazek (placeholder) -->
                        <div class="h-48 bg-gray-200 flex items-center justify-center text-gray-400 overflow-hidden relative">
                            @if($car->image_path)
                                <img src="{{ asset('storage/' . $car->image_path) }}" alt="{{ $car->brand->name }} {{ $car->model }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="text-center">
                                    <span class="text-4xl block">🚗</span>
                                    <span class="text-xs mt-2">Brak zdjęcia</span>
                                </div>
                            @endif
                            
                            <!-- Badge dostępności (tylko ogólny status auta) -->
                            @if(!$car->is_available)
                                <span class="absolute top-2 right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full shadow">
                                    Serwis / Niedostępny
                                </span>
                            @endif
                        </div>

                        <div class="p-4 flex-grow">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="text-lg font-bold text-gray-900 leading-tight">
                                    {{ $car->brand->name }} {{ $car->model }}
                                </h3>
                            </div>
                            
                            <p class="text-sm text-gray-600 mb-3 flex items-center gap-2">
                                <span class="bg-gray-100 px-2 py-0.5 rounded text-xs">{{ $car->year }}</span>
                                <span class="bg-gray-100 px-2 py-0.5 rounded text-xs">{{ $car->type->name }}</span>
                            </p>

                            <!-- Cechy (Features) -->
                            <div class="flex flex-wrap gap-1 mb-4">
                                @foreach($car->features->take(3) as $feature)
                                    <span class="inline-block bg-blue-50 text-blue-700 text-[10px] px-2 py-1 rounded border border-blue-100">
                                        {{ $feature->name }}
                                    </span>
                                @endforeach
                                @if($car->features->count() > 3)
                                    <span class="text-xs text-gray-500 self-center ml-1">+{{ $car->features->count() - 3 }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="p-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                            <div>
                                <span class="text-xl font-bold text-blue-600">{{ number_format($car->daily_rate, 0) }} zł</span>
                                <span class="text-xs text-gray-500 block">/ dzień</span>
                            </div>
                            <a href="{{ route('cars.show', $car) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Szczegóły
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="col-span-3 p-12 text-center bg-white rounded-lg shadow-sm border border-dashed border-gray-300">
                        <div class="text-5xl mb-4">🔍</div>
                        <h3 class="text-lg font-medium text-gray-900">Brak samochodów</h3>
                        <p class="text-gray-500 mt-1">
                            @if(request('start_date'))
                                Wszystkie auta są zajęte w wybranym terminie. Spróbuj zmienić daty.
                            @else
                                Nie znaleziono pojazdów spełniających Twoje kryteria.
                            @endif
                        </p>
                        <a href="{{ route('cars.index') }}" class="mt-4 inline-block text-blue-600 font-semibold hover:underline">Wyczyść filtry</a>
                    </div>
                @endforelse
            </div>

            <!-- Paginacja -->
            <div class="mt-8">
                {{ $cars->links() }}
            </div>
        </div>
    </div>
@endsection