@extends('layouts.app')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        
        <!-- Panel boczny: Filtry -->
        <aside class="md:col-span-1">
            <div class="bg-white p-4 rounded-lg shadow sticky top-4">
                <h2 class="text-lg font-semibold mb-4 border-b pb-2">Filtrowanie</h2>
                <form action="{{ route('cars.index') }}" method="GET">
                    
                    <!-- Dostępność (Daty) -->
                    <div class="mb-4 bg-blue-50 p-3 rounded-md border border-blue-100">
                        <p class="text-xs font-bold text-blue-800 mb-2 uppercase">Termin wynajmu</p>
                        <div class="mb-2">
                            <label for="start_date" class="block text-xs font-medium text-gray-700">Od</label>
                            <input type="date" name="start_date" id="start_date" 
                                   value="{{ request('start_date') }}" min="{{ date('Y-m-d') }}"
                                   class="w-full text-xs border-gray-300 rounded focus:border-blue-500 focus:ring-blue-200">
                        </div>
                        <div>
                            <label for="end_date" class="block text-xs font-medium text-gray-700">Do</label>
                            <input type="date" name="end_date" id="end_date" 
                                   value="{{ request('end_date') }}" min="{{ date('Y-m-d') }}"
                                   class="w-full text-xs border-gray-300 rounded focus:border-blue-500 focus:ring-blue-200">
                        </div>
                    </div>

                    <!-- Oddział -->
                    <div class="mb-3">
                        <label for="branch" class="block text-sm font-medium text-gray-700 mb-1">Oddział / Miasto</label>
                        <select name="branch" id="branch" class="w-full text-sm border-gray-300 rounded-md shadow-sm">
                            <option value="">Wszystkie lokalizacje</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ request('branch') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->city }} ({{ $branch->name }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Marka -->
                    <div class="mb-3">
                        <label for="brand" class="block text-sm font-medium text-gray-700 mb-1">Marka</label>
                        <select name="brand" id="brand" class="w-full text-sm border-gray-300 rounded-md shadow-sm">
                            <option value="">Wszystkie marki</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ request('brand') == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Typ -->
                    <div class="mb-3">
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Klasa pojazdu</label>
                        <select name="type" id="type" class="w-full text-sm border-gray-300 rounded-md shadow-sm">
                            <option value="">Wszystkie</option>
                            @foreach($types as $type)
                                <option value="{{ $type->id }}" {{ request('type') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Skrzynia Biegów -->
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Skrzynia biegów</label>
                        <select name="transmission" class="w-full text-sm border-gray-300 rounded-md shadow-sm">
                            <option value="">Dowolna</option>
                            <option value="manual" {{ request('transmission') == 'manual' ? 'selected' : '' }}>Manualna</option>
                            <option value="automatic" {{ request('transmission') == 'automatic' ? 'selected' : '' }}>Automatyczna</option>
                        </select>
                    </div>

                    <!-- Cena -->
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cena za dobę (zł)</label>
                        <div class="flex gap-2">
                            <input type="number" name="min_price" placeholder="Od" value="{{ request('min_price') }}" class="w-1/2 text-sm border-gray-300 rounded">
                            <input type="number" name="max_price" placeholder="Do" value="{{ request('max_price') }}" class="w-1/2 text-sm border-gray-300 rounded">
                        </div>
                    </div>

                    <!-- Rocznik -->
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rocznik</label>
                        <div class="flex gap-2">
                            <input type="number" name="year_from" placeholder="Od" value="{{ request('year_from') }}" class="w-1/2 text-sm border-gray-300 rounded">
                            <input type="number" name="year_to" placeholder="Do" value="{{ request('year_to') }}" class="w-1/2 text-sm border-gray-300 rounded">
                        </div>
                    </div>

                    <!-- Wyposażenie (Checkboxy) -->
                    <div class="mb-4 border-t pt-2">
                        <p class="block text-sm font-medium text-gray-700 mb-2">Wyposażenie (zawiera)</p>
                        <div class="space-y-1 max-h-40 overflow-y-auto">
                            @foreach($features as $feature)
                                <div class="flex items-center">
                                    <input type="checkbox" name="features[]" value="{{ $feature->id }}" id="f_{{ $feature->id }}"
                                           class="h-3 w-3 text-blue-600 border-gray-300 rounded"
                                           {{ in_array($feature->id, request('features', [])) ? 'checked' : '' }}>
                                    <label for="f_{{ $feature->id }}" class="ml-2 text-xs text-gray-600">{{ $feature->name }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition shadow font-bold text-sm">
                        Szukaj
                    </button>
                    
                    <a href="{{ route('cars.index') }}" class="block text-center mt-3 text-xs text-gray-500 hover:text-gray-900 underline">
                        Wyczyść wszystkie filtry
                    </a>
                </form>
            </div>
        </aside>

        <!-- Lista samochodów -->
        <div class="md:col-span-3">
            @if(request('start_date') || request('branch') || request('transmission'))
                <div class="mb-4 p-3 bg-gray-50 rounded border border-gray-200 text-sm text-gray-600 flex flex-wrap gap-2 items-center">
                    <span class="font-bold">Aktywne filtry:</span>
                    @if(request('start_date')) <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">📅 {{ request('start_date') }} - {{ request('end_date') }}</span> @endif
                    @if(request('branch')) <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">🏢 Wybrany oddział</span> @endif
                    @if(request('transmission')) <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">⚙️ {{ request('transmission') }}</span> @endif
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($cars as $car)
                    <article class="bg-white rounded-lg shadow overflow-hidden flex flex-col h-full transition hover:shadow-lg group">
                        <div class="h-48 bg-gray-200 flex items-center justify-center text-gray-400 overflow-hidden relative">
                            @if($car->image_path)
                                <img src="{{ asset('storage/' . $car->image_path) }}" alt="{{ $car->brand->name }} {{ $car->model }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="text-center">
                                    <span class="text-4xl block">🚗</span>
                                    <span class="text-xs mt-2">Brak zdjęcia</span>
                                </div>
                            @endif
                            
                            @if(!$car->is_available)
                                <span class="absolute top-2 right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full shadow">Niedostępny</span>
                            @endif
                        </div>

                        <div class="p-4 flex-grow">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="text-lg font-bold text-gray-900 leading-tight">
                                    {{ $car->brand->name }} {{ $car->model }}
                                </h3>
                            </div>
                            
                            <div class="text-xs text-gray-600 mb-3 space-y-1">
                                <p class="flex items-center gap-1">
                                    <span>📅 {{ $car->year }}</span> • 
                                    <span>⚙️ {{ $car->transmission == 'automatic' ? 'Automat' : 'Manual' }}</span>
                                </p>
                                <p class="flex items-center gap-1">
                                    <span>📍 {{ $car->branch->city }}</span>
                                </p>
                            </div>

                            <div class="flex flex-wrap gap-1 mb-4">
                                @foreach($car->features->take(3) as $feature)
                                    <span class="inline-block bg-gray-100 text-gray-600 text-[10px] px-2 py-1 rounded border">
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
                            <a href="{{ route('cars.show', $car) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 transition">
                                Szczegóły
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="col-span-3 p-12 text-center bg-white rounded-lg shadow-sm border border-dashed border-gray-300">
                        <div class="text-5xl mb-4">🔍</div>
                        <h3 class="text-lg font-medium text-gray-900">Brak samochodów</h3>
                        <p class="text-gray-500 mt-1">Nie znaleziono pojazdów spełniających Twoje kryteria.</p>
                        <a href="{{ route('cars.index') }}" class="mt-4 inline-block text-blue-600 font-semibold hover:underline">Wyczyść wszystkie filtry</a>
                    </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $cars->links() }}
            </div>
        </div>
    </div>
@endsection