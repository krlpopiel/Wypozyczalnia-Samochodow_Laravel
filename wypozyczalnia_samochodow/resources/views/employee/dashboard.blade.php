@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow overflow-hidden border border-gray-200">
    <div class="p-6 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-center gap-4">
        <h1 class="text-xl font-bold text-gray-800" id="reservations-heading">Zarządzanie Rezerwacjami</h1>
        
        <nav aria-label="Filtruj rezerwacje po statusie">
            <ul class="flex space-x-2">
                <li>
                    <a href="{{ route('employee.dashboard') }}" 
                       class="inline-block px-3 py-1 text-sm font-medium rounded-full border focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 {{ !request('status') ? 'bg-blue-700 text-white border-blue-800' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}"
                       @if(!request('status')) aria-current="page" @endif>
                       Wszystkie <span class="sr-only">rezerwacje</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('employee.dashboard', ['status' => 'pending']) }}" 
                       class="inline-block px-3 py-1 text-sm font-medium rounded-full border focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 {{ request('status') == 'pending' ? 'bg-yellow-600 text-white border-yellow-700' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}"
                       @if(request('status') == 'pending') aria-current="page" @endif>
                       Oczekujące <span class="sr-only">rezerwacje</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('employee.dashboard', ['status' => 'confirmed']) }}" 
                       class="inline-block px-3 py-1 text-sm font-medium rounded-full border focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 {{ request('status') == 'confirmed' ? 'bg-green-700 text-white border-green-800' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}"
                       @if(request('status') == 'confirmed') aria-current="page" @endif>
                       Potwierdzone <span class="sr-only">rezerwacje</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <div class="overflow-x-auto" tabindex="0" role="region" aria-labelledby="reservations-heading">
        <table class="min-w-full divide-y divide-gray-200" aria-label="Tabela rezerwacji klientów">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">ID / Klient</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Samochód</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Termin</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Trasa</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Uwagi</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Akcje</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($rentals as $rental)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-xs text-gray-500 font-mono" aria-label="Numer rezerwacji">#{{ $rental->id }}</div>
                        <div class="text-sm font-bold text-gray-900"><span class="sr-only">Klient: </span>{{ $rental->user->name }}</div>
                        <div class="text-xs text-gray-600"><span class="sr-only">Email: </span>{{ $rental->user->email }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900"><span class="sr-only">Samochód: </span>{{ $rental->car->brand->name ?? '' }} {{ $rental->car->model }}</div>
                        <div class="text-xs text-gray-600 font-mono uppercase"><span class="sr-only">Rejestracja: </span>{{ $rental->car->registration_plate }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        <div class="flex flex-col">
                            <span><span class="sr-only">Od dnia: </span>{{ $rental->start_date->format('Y-m-d') }}</span>
                            <span class="text-xs text-gray-500" aria-hidden="true">↓</span>
                            <span><span class="sr-only">Do dnia: </span>{{ $rental->end_date->format('Y-m-d') }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <div class="flex flex-col gap-1">
                            <div class="flex items-center text-green-800">
                                <span class="w-14 text-xs text-gray-500 uppercase font-semibold" aria-hidden="true">Odbiór</span> 
                                <span class="sr-only">Miejsce odbioru:</span>
                                <strong>{{ $rental->originBranch->city ?? '?' }}</strong>
                            </div>
                            <div class="flex items-center text-blue-800">
                                <span class="w-14 text-xs text-gray-500 uppercase font-semibold" aria-hidden="true">Zwrot</span> 
                                <span class="sr-only">, Miejsce zwrotu:</span>
                                <strong>{{ $rental->destinationBranch->city ?? '?' }}</strong>
                            </div>
                            @if($rental->origin_branch_id != $rental->destination_branch_id)
                                <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded border border-yellow-200 w-fit font-bold">Relokacja</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600 max-w-xs">
                        @if($rental->comments)
                            <div class="group relative" tabindex="0">
                                <span class="truncate block w-full border-b border-dotted border-gray-400 cursor-help">
                                    <span class="sr-only">Uwagi: </span> {{ Str::limit($rental->comments, 15) }}
                                </span>
                                <div class="hidden group-hover:block group-focus:block absolute left-0 bottom-full mb-2 w-64 p-3 bg-gray-800 text-white text-xs rounded z-50 shadow-lg">
                                    {{ $rental->comments }}
                                </div>
                            </div>
                        @else
                            <span class="text-gray-400" aria-hidden="true">-</span>
                            <span class="sr-only">Brak uwag</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex flex-col">
                            <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full border {{ $colors[$rental->status->name] ?? 'bg-gray-100' }} w-fit">
                                <span class="sr-only">Status: </span>{{ $rental->status->label }}
                            </span>
                            <span class="text-xs font-bold mt-1 text-gray-700">
                                <span class="sr-only">, Kwota: </span>{{ number_format($rental->total_price, 2) }} zł
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex justify-end gap-2">
                            @if($rental->status->name === 'pending')
                                <form action="{{ route('employee.rentals.status', $rental) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="confirmed">
                                    <button type="submit" class="text-green-700 hover:text-green-900 font-bold focus:outline-none focus:ring-2 focus:ring-green-500 rounded px-2" aria-label="Potwierdź rezerwację {{ $rental->id }}">✔</button>
                                </form>
                                <form action="{{ route('employee.rentals.status', $rental) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="cancelled">
                                    <button type="submit" class="text-red-700 hover:text-red-900 font-bold focus:outline-none focus:ring-2 focus:ring-red-500 rounded px-2" aria-label="Odrzuć rezerwację {{ $rental->id }}">✘</button>
                                </form>
                            @elseif($rental->status->name === 'confirmed')
                                <form action="{{ route('employee.rentals.status', $rental) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="ongoing">
                                    <button type="submit" class="text-blue-700 hover:text-blue-900 font-bold focus:outline-none focus:ring-2 focus:ring-blue-500 rounded px-2" aria-label="Wydaj samochód dla rezerwacji {{ $rental->id }}">WYDAJ</button>
                                </form>
                            @elseif($rental->status->name === 'ongoing')
                                <form action="{{ route('employee.rentals.status', $rental) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="completed">
                                    <button type="submit" class="text-purple-700 hover:text-purple-900 font-bold focus:outline-none focus:ring-2 focus:ring-purple-500 rounded px-2" aria-label="Zatwierdź zwrot dla rezerwacji {{ $rental->id }}">ZWRÓĆ</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-200">
        {{ $rentals->withQueryString()->links() }}
    </div>
</div>
@endsection