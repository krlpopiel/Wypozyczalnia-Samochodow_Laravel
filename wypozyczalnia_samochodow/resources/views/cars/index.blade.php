@extends('layouts.app')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        
        <!-- Panel boczny: Filtry -->
        <aside class="md:col-span-1">
            <div class="bg-white p-4 rounded-lg shadow">
                <h2 class="text-lg font-semibold mb-4 border-b pb-2">Filtrowanie</h2>
                <form action="{{ route('cars.index') }}" method="GET">
                    
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
                        <label for="max_price" class="block text-sm font-medium text-gray-700 mb-1">Cena maksymalna (zł/dzień)</label>
                        <input type="number" name="max_price" id="max_price" 
                               value="{{ request('max_price') }}" 
                               class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                               placeholder="np. 300">
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Filtruj wyniki
                    </button>
                    
                    <a href="{{ route('cars.index') }}" class="block text-center mt-3 text-sm text-gray-600 hover:text-gray-900 underline">
                        Wyczyść filtry
                    </a>
                </form>
            </div>
        </aside>

        <!-- Lista samochodów -->
        <div class="md:col-span-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($cars as $car)
                    <article class="bg-white rounded-lg shadow overflow-hidden flex flex-col h-full transition hover:shadow-lg">
                        <!-- Obrazek (placeholder) -->
                        <div class="h-48 bg-gray-200 flex items-center justify-center text-gray-400">
                            @if($car->image_path)
                                <img src="{{ asset('storage/' . $car->image_path) }}" alt="{{ $car->brand->name }} {{ $car->model }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-4xl">🚗</span>
                            @endif
                        </div>

                        <div class="p-4 flex-grow">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="text-lg font-bold text-gray-900">
                                    {{ $car->brand->name }} {{ $car->model }}
                                </h3>
                                <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                                    {{ $car->type->name }}
                                </span>
                            </div>
                            
                            <p class="text-sm text-gray-600 mb-4">
                                Rocznik: {{ $car->year }} | Kolor: {{ $car->color }}
                            </p>

                            <!-- Cechy (Features) -->
                            <div class="flex flex-wrap gap-1 mb-4">
                                @foreach($car->features->take(3) as $feature)
                                    <span class="inline-block bg-gray-100 text-gray-600 text-[10px] px-2 py-1 rounded-full border">
                                        {{ $feature->name }}
                                    </span>
                                @endforeach
                                @if($car->features->count() > 3)
                                    <span class="text-xs text-gray-500 self-center">+{{ $car->features->count() - 3 }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="p-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                            <div>
                                <span class="text-2xl font-bold text-blue-600">{{ $car->daily_rate }} zł</span>
                                <span class="text-xs text-gray-500 block">za dzień</span>
                            </div>
                            <a href="{{ route('cars.show', $car) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150" aria-label="Zobacz szczegóły i wynajmij {{ $car->brand->name }} {{ $car->model }}">
                                Wybierz
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="col-span-3 p-8 text-center bg-white rounded-lg shadow">
                        <p class="text-xl text-gray-600">Nie znaleziono samochodów spełniających kryteria.</p>
                        <a href="{{ route('cars.index') }}" class="mt-4 inline-block text-blue-600 font-bold hover:underline">Pokaż wszystkie</a>
                    </div>
                @endforelse
            </div>

            <!-- Paginacja -->
            <div class="mt-6">
                {{ $cars->links() }}
            </div>
        </div>
    </div>
@endsection