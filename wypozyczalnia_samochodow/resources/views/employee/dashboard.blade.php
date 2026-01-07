@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-xl font-bold text-gray-800">Panel Zarządzania Rezerwacjami</h2>
        
        <div class="flex space-x-2">
            <a href="{{ route('employee.dashboard') }}" class="px-3 py-1 text-xs rounded-full {{ !request('status') ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">Wszystkie</a>
            <a href="{{ route('employee.dashboard', ['status' => 'pending']) }}" class="px-3 py-1 text-xs rounded-full {{ request('status') == 'pending' ? 'bg-yellow-500 text-white' : 'bg-gray-200 text-gray-700' }}">Oczekujące</a>
            <a href="{{ route('employee.dashboard', ['status' => 'confirmed']) }}" class="px-3 py-1 text-xs rounded-full {{ request('status') == 'confirmed' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700' }}">Potwierdzone</a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID/Klient</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Samochód</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Termin</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trasa (Oddziały)</th> <!-- Nowa kolumna -->
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Akcje</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($rentals as $rental)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-xs text-gray-500">#{{ $rental->id }}</div>
                        <div class="text-sm font-bold text-gray-900">{{ $rental->user->name }}</div>
                        <div class="text-xs text-gray-500">{{ $rental->user->email }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $rental->car->brand->name ?? '' }} {{ $rental->car->model }}</div>
                        <div class="text-xs text-gray-500">{{ $rental->car->registration_plate }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $rental->start_date->format('Y-m-d') }} <br> 
                        -> {{ $rental->end_date->format('Y-m-d') }}
                    </td>
                    
                    <!-- Kolumna Trasa -->
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <div class="flex flex-col gap-1">
                            <span class="flex items-center text-green-700">
                                <span class="w-16 text-xs text-gray-400">Odbiór:</span> 
                                <strong>{{ $rental->originBranch->city ?? '?' }}</strong>
                            </span>
                            <span class="flex items-center text-blue-700">
                                <span class="w-16 text-xs text-gray-400">Zwrot:</span> 
                                <strong>{{ $rental->destinationBranch->city ?? '?' }}</strong>
                            </span>
                            @if($rental->origin_branch_id != $rental->destination_branch_id)
                                <span class="text-xs bg-yellow-100 text-yellow-800 px-1 rounded w-fit">Relokacja</span>
                            @endif
                        </div>
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $colors = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'confirmed' => 'bg-blue-100 text-blue-800',
                                'ongoing' => 'bg-indigo-100 text-indigo-800',
                                'completed' => 'bg-green-100 text-green-800',
                                'cancelled' => 'bg-red-100 text-red-800',
                            ];
                            $color = $colors[$rental->status->name] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <div class="flex flex-col">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }} w-fit">
                                {{ $rental->status->label }}
                            </span>
                            <span class="text-xs font-bold mt-1 text-gray-600">{{ number_format($rental->total_price, 2) }} zł</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex justify-end gap-2">
                            @if($rental->status->name === 'pending')
                                <form action="{{ route('employee.rentals.status', $rental) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="confirmed">
                                    <button type="submit" class="text-green-600 hover:text-green-900 font-bold" title="Potwierdź">✔</button>
                                </form>
                                <form action="{{ route('employee.rentals.status', $rental) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="cancelled">
                                    <button type="submit" class="text-red-600 hover:text-red-900 font-bold" title="Odrzuć">✘</button>
                                </form>
                            @elseif($rental->status->name === 'confirmed')
                                <form action="{{ route('employee.rentals.status', $rental) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="ongoing">
                                    <button type="submit" class="text-blue-600 hover:text-blue-900 font-bold">WYDAJ</button>
                                </form>
                            @elseif($rental->status->name === 'ongoing')
                                <form action="{{ route('employee.rentals.status', $rental) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="completed">
                                    <button type="submit" class="text-purple-600 hover:text-purple-900 font-bold">ZWRÓĆ</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">Brak rezerwacji.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-200">
        {{ $rentals->withQueryString()->links() }}
    </div>
</div>
@endsection