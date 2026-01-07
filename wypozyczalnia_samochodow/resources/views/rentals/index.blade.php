@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
        <div class="p-6 bg-white border-b border-gray-200">
            <h1 class="text-2xl font-bold mb-6 text-gray-800">Moje Rezerwacje</h1>

            @if($rentals->isEmpty())
                <div class="text-center py-10 text-gray-600">
                    <p class="text-lg">Nie masz jeszcze żadnych rezerwacji.</p>
                    <a href="{{ route('cars.index') }}" class="mt-4 inline-block text-blue-700 hover:underline font-medium">
                        Przeglądaj samochody
                    </a>
                </div>
            @else
                <div class="overflow-x-auto" tabindex="0">
                    <table class="min-w-full divide-y divide-gray-200" aria-label="Lista twoich rezerwacji">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Samochód</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Termin</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Odbiór / Zwrot</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Koszt</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Akcje</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($rentals as $rental)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center text-xl" aria-hidden="true">🚗</div>
                                            <div class="ml-4">
                                                <div class="text-sm font-bold text-gray-900">
                                                    <span class="sr-only">Samochód: </span>{{ $rental->car->brand->name ?? 'Marka' }} {{ $rental->car->model }}
                                                </div>
                                                <div class="text-sm text-gray-600">
                                                    <span class="sr-only">, Rejestracja: </span>{{ $rental->car->registration_plate }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            <span class="sr-only">Od dnia: </span>{{ $rental->start_date->format('Y-m-d') }}
                                        </div>
                                        <div class="text-sm text-gray-600">
                                            <span class="sr-only">Do dnia: </span>{{ $rental->end_date->format('Y-m-d') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <div class="flex flex-col gap-1">
                                            <div><span class="text-xs text-gray-500 uppercase font-semibold">Odbiór:</span> <strong>{{ $rental->originBranch->city ?? '-' }}</strong></div>
                                            <div><span class="text-xs text-gray-500 uppercase font-semibold">Zwrot:</span> <strong>{{ $rental->destinationBranch->city ?? '-' }}</strong></div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">
                                        {{ number_format($rental->total_price, 2) }} zł
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border bg-gray-100 text-gray-800 border-gray-200">
                                            {{ $rental->status->label ?? $rental->status->name }}
                                        </span>
                                    </td>
                                    
                                    <!-- LOGIKA PRZYCISKU OPINII -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @if($rental->status->name === 'completed')
                                            @if($rental->review)
                                                <!-- Jeśli opinia już istnieje -->
                                                <div class="flex items-center text-yellow-500" title="Już oceniono">
                                                    <span class="font-bold mr-1">{{ $rental->review->rating }}</span>
                                                    <span aria-hidden="true">★</span>
                                                    <span class="sr-only">Wystawiono ocenę {{ $rental->review->rating }} na 5</span>
                                                </div>
                                            @else
                                                <!-- Jeśli brak opinii -->
                                                <a href="{{ route('reviews.create', $rental) }}" class="text-indigo-600 hover:text-indigo-900 font-bold underline focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded px-1">
                                                    Wystaw opinię
                                                </a>
                                            @endif
                                        @elseif($rental->status->name === 'pending')
                                            <span class="text-gray-500 italic">Oczekiwanie...</span>
                                        @else
                                            <span class="text-gray-400" aria-hidden="true">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $rentals->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection