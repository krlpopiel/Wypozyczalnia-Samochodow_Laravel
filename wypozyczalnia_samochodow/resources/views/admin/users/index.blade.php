@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
        <h1 class="text-2xl font-bold text-gray-800">Zarządzanie Użytkownikami</h1>
        
        <!-- Wyszukiwarka -->
        <form action="{{ route('admin.users.index') }}" method="GET" class="flex gap-2 w-full sm:w-auto" role="search">
            <label for="search_users" class="sr-only">Szukaj użytkownika</label>
            <input type="text" id="search_users" name="search" value="{{ request('search') }}" 
                   placeholder="Imię lub email..." 
                   class="text-sm border-gray-300 rounded-md w-full sm:w-64 focus:ring-blue-500 focus:border-blue-500 text-gray-900">
            <button type="submit" class="bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 rounded text-sm transition focus:ring-2 focus:ring-offset-2 focus:ring-gray-700 font-bold">
                Szukaj
            </button>
            @if(request('search'))
                <a href="{{ route('admin.users.index') }}" class="text-red-700 hover:text-red-900 text-sm flex items-center font-medium focus:outline-none focus:underline px-2" aria-label="Wyczyść wyszukiwanie">
                    Wyczyść
                </a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden border border-gray-200">
        <div class="overflow-x-auto" tabindex="0">
            <table class="min-w-full divide-y divide-gray-200">
                <caption class="sr-only">Lista zarejestrowanych użytkowników w systemie</caption>
                <thead class="bg-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Dane użytkownika</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Rola</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Data rejestracji</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Akcje</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            <span class="sr-only">ID: </span>{{ $user->id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-gray-900">{{ $user->name }}</div>
                            <div class="text-xs text-gray-600"><a href="mailto:{{ $user->email }}" class="hover:underline focus:outline-none focus:ring-2 focus:ring-blue-500 rounded">{{ $user->email }}</a></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $roles = [
                                    'admin' => ['label' => 'Administrator', 'class' => 'bg-purple-100 text-purple-800 border-purple-200'],
                                    'employee' => ['label' => 'Pracownik', 'class' => 'bg-blue-100 text-blue-800 border-blue-200'],
                                    'client' => ['label' => 'Klient', 'class' => 'bg-gray-100 text-gray-800 border-gray-200'],
                                ];
                                $roleConfig = $roles[$user->role] ?? $roles['client'];
                            @endphp
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border {{ $roleConfig['class'] }}">
                                {{ $roleConfig['label'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $user->created_at->format('Y-m-d') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end gap-3">
                                <a href="{{ route('admin.users.edit', $user) }}" class="text-indigo-700 hover:text-indigo-900 underline focus:outline-none focus:text-indigo-900 focus:ring-2 focus:ring-indigo-500 rounded px-1" aria-label="Edytuj użytkownika {{ $user->name }}">
                                    Edytuj
                                </a>
                                
                                @if(Auth::id() !== $user->id)
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Czy na pewno chcesz usunąć użytkownika {{ $user->name }}? Tej operacji nie można cofnąć.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-700 hover:text-red-900 underline focus:outline-none focus:text-red-900 focus:ring-2 focus:ring-red-500 rounded px-1" aria-label="Usuń użytkownika {{ $user->name }}">
                                            Usuń
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500 italic">Nie znaleziono użytkowników.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-200">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection