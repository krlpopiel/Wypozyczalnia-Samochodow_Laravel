@extends('layouts.app')

@section('content')
<div class="space-y-8">
    
    <h1 class="sr-only">Panel Zarządzania Systemem</h1>

    <!-- Sekcja Samochodów -->
    <section class="bg-white rounded-lg shadow overflow-hidden border border-gray-200" aria-labelledby="fleet-heading">
        <div class="p-6 border-b border-gray-200 flex flex-col md:flex-row justify-between items-center bg-gray-50 gap-4">
            <h2 id="fleet-heading" class="text-xl font-bold text-gray-800">Zarządzanie Flotą Samochodową</h2>
            
            <div class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
                <form action="{{ route('employee.management') }}" method="GET" class="flex gap-2">
                    <label for="search_car" class="sr-only">Szukaj samochodu</label>
                    <input type="text" id="search_car" name="search_car" value="{{ request('search_car') }}" 
                           placeholder="Szukaj (marka, model, rej.)..." 
                           class="text-sm border-gray-300 rounded-md w-full md:w-64 focus:ring-blue-500 focus:border-blue-500 text-gray-900">
                    <button type="submit" class="bg-gray-700 hover:bg-gray-800 text-white px-3 py-2 rounded text-sm transition focus:ring-2 focus:ring-offset-2 focus:ring-gray-700">
                        Szukaj
                    </button>
                    @if(request('search_car'))
                        <a href="{{ route('employee.management') }}" class="text-red-600 hover:text-red-800 text-sm flex items-center font-medium" aria-label="Wyczyść wyszukiwanie">
                            ✕
                        </a>
                    @endif
                </form>

                <a href="{{ route('cars.create') }}" class="bg-blue-700 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded text-sm text-center transition focus:ring-2 focus:ring-offset-2 focus:ring-blue-700">
                    + Dodaj Nowy
                </a>
            </div>
        </div>
        
        <div class="overflow-x-auto" tabindex="0">
            <table class="min-w-full divide-y divide-gray-200" aria-label="Lista wszystkich samochodów">
                <thead class="bg-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Pojazd</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Rejestracja</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Oddział</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Cena/dzień</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Akcje</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($cars as $car)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">#{{ $car->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-gray-900">{{ $car->brand->name }} {{ $car->model }}</div>
                            <div class="text-xs text-gray-600">{{ $car->year }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 font-mono">{{ $car->registration_plate }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $car->branch->city }} ({{ $car->branch->name }})</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">{{ $car->daily_rate }} zł</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('cars.edit', $car) }}" class="text-indigo-700 hover:text-indigo-900 mr-3 underline focus:outline-none focus:text-indigo-900">Edytuj</a>
                            <form action="{{ route('cars.destroy', $car) }}" method="POST" class="inline" onsubmit="return confirm('Czy na pewno usunąć ten samochód?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-700 hover:text-red-900 underline focus:outline-none focus:text-red-900">Usuń</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500 italic">Brak samochodów w bazie.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-200">
            {{ $cars->links() }}
        </div>
    </section>

    <!-- Grid dla Słowników -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        
        <!-- Sekcja Marek -->
        <section class="bg-white rounded-lg shadow overflow-hidden border border-gray-200" aria-labelledby="brands-heading">
            <div class="p-4 bg-gray-50 border-b flex justify-between items-center">
                <h3 id="brands-heading" class="font-bold text-gray-800">Marki Samochodów</h3>
            </div>
            <div class="p-4 border-b bg-gray-50">
                <form action="{{ route('employee.brands.store') }}" method="POST" class="flex gap-2">
                    @csrf
                    <label for="brand_name" class="sr-only">Nazwa marki</label>
                    <input type="text" id="brand_name" name="name" placeholder="Nowa marka (np. Tesla)" required class="flex-1 text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <button type="submit" class="bg-indigo-600 text-white px-3 py-2 rounded text-sm hover:bg-indigo-700 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600">Dodaj</button>
                </form>
            </div>
            <ul class="divide-y divide-gray-100 max-h-60 overflow-y-auto" role="list">
                @foreach($brands as $brand)
                <li class="px-4 py-3 flex justify-between items-center text-sm hover:bg-gray-50">
                    <span class="text-gray-900">{{ $brand->name }}</span>
                    <form action="{{ route('employee.brands.destroy', $brand) }}" method="POST" onsubmit="return confirm('Usunąć markę?');">
                        @csrf @method('DELETE')
                        <button class="text-red-600 hover:text-red-800 underline focus:outline-none focus:text-red-800" aria-label="Usuń markę {{ $brand->name }}">Usuń</button>
                    </form>
                </li>
                @endforeach
            </ul>
        </section>

        <!-- Sekcja Wyposażenia -->
        <section class="bg-white rounded-lg shadow overflow-hidden border border-gray-200" aria-labelledby="features-heading">
            <div class="p-4 bg-gray-50 border-b flex justify-between items-center">
                <h3 id="features-heading" class="font-bold text-gray-800">Opcje Wyposażenia</h3>
            </div>
            <div class="p-4 border-b bg-gray-50">
                <form action="{{ route('employee.features.store') }}" method="POST" class="flex gap-2">
                    @csrf
                    <label for="feature_name" class="sr-only">Nazwa wyposażenia</label>
                    <input type="text" id="feature_name" name="name" placeholder="Nowe wyposażenie (np. Hak)" required class="flex-1 text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <button type="submit" class="bg-indigo-600 text-white px-3 py-2 rounded text-sm hover:bg-indigo-700 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600">Dodaj</button>
                </form>
            </div>
            <ul class="divide-y divide-gray-100 max-h-60 overflow-y-auto" role="list">
                @foreach($features as $feature)
                <li class="px-4 py-3 flex justify-between items-center text-sm hover:bg-gray-50">
                    <span class="text-gray-900">{{ $feature->name }}</span>
                    <form action="{{ route('employee.features.destroy', $feature) }}" method="POST" onsubmit="return confirm('Usunąć to wyposażenie?');">
                        @csrf @method('DELETE')
                        <button class="text-red-600 hover:text-red-800 underline focus:outline-none focus:text-red-800" aria-label="Usuń wyposażenie {{ $feature->name }}">Usuń</button>
                    </form>
                </li>
                @endforeach
            </ul>
        </section>
    </div>

    <!-- Sekcja Oddziałów -->
    <section class="bg-white rounded-lg shadow overflow-hidden border border-gray-200" aria-labelledby="branches-heading">
        <!-- Sekcja Oddziałów -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6 border-b border-gray-200 bg-gray-50">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Zarządzanie Oddziałami</h2>
            
            <!-- Formularz dodawania oddziału -->
            <form action="{{ route('employee.branches.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-700">Nazwa</label>
                    <input type="text" name="name" required placeholder="np. Centrum" class="mt-1 w-full text-sm border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700">Adres</label>
                    <input type="text" name="address" required placeholder="Ulica 123" class="mt-1 w-full text-sm border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700">Miasto</label>
                    <input type="text" name="city" required placeholder="Warszawa" class="mt-1 w-full text-sm border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700">Telefon</label>
                    <input type="text" name="phone" required placeholder="123-456-789" class="mt-1 w-full text-sm border-gray-300 rounded-md">
                </div>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm transition">
                    Dodaj Oddział
                </button>
            </form>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Miasto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nazwa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Adres</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telefon</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Akcje</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($branches as $branch)
                    <tr>
                        <form action="{{ route('employee.branches.update', $branch) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="text" name="city" value="{{ $branch->city }}" class="text-sm border-gray-300 rounded w-24 px-2 py-1">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="text" name="name" value="{{ $branch->name }}" class="text-sm border-gray-300 rounded w-32 px-2 py-1">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="text" name="address" value="{{ $branch->address }}" class="text-sm border-gray-300 rounded w-32 px-2 py-1">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="text" name="phone" value="{{ $branch->phone }}" class="text-sm border-gray-300 rounded w-28 px-2 py-1">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button type="submit" class="text-blue-600 hover:text-blue-900 mr-2" title="Zapisz zmiany">💾</button>
                        </form>
                                <form action="{{ route('employee.branches.destroy', $branch) }}" method="POST" class="inline" onsubmit="return confirm('Usunąć ten oddział?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Usuń oddział">🗑</button>
                                </form>
                            </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    </section>
</div>
@endsection