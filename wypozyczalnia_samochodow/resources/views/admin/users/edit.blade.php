@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow-md border border-gray-200">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Edytuj Użytkownika</h1>
        <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-gray-900 underline focus:ring-2 focus:ring-blue-500 rounded px-1">
            &larr; Wróć do listy
        </a>
    </div>

    <form action="{{ route('admin.users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Imię i Nazwisko -->
        <div class="mb-4">
            <label for="name" class="block text-sm font-bold text-gray-700 mb-1">Imię i Nazwisko</label>
            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required 
                   class="w-full text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-gray-900">
            @error('name')
                <p class="text-red-600 text-xs mt-1" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email -->
        <div class="mb-4">
            <label for="email" class="block text-sm font-bold text-gray-700 mb-1">Adres Email</label>
            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required 
                   class="w-full text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-gray-900">
            @error('email')
                <p class="text-red-600 text-xs mt-1" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <!-- Rola (Kluczowy element) -->
        <div class="mb-6 bg-yellow-50 p-4 rounded-md border border-yellow-200">
            <label for="role" class="block text-sm font-bold text-yellow-900 mb-2">Rola w systemie</label>
            <select name="role" id="role" class="w-full text-sm border-yellow-300 rounded-md shadow-sm text-gray-900 focus:border-yellow-500 focus:ring-yellow-500">
                <option value="client" {{ old('role', $user->role) == 'client' ? 'selected' : '' }}>Klient (Dostęp podstawowy)</option>
                <option value="employee" {{ old('role', $user->role) == 'employee' ? 'selected' : '' }}>Pracownik (Zarządzanie rezerwacjami i flotą)</option>
                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrator (Pełny dostęp)</option>
            </select>
            <p class="text-xs text-yellow-800 mt-2">
                <strong>Uwaga:</strong> Zmiana roli na "Administrator" lub "Pracownik" da temu użytkownikowi dostęp do wrażliwych danych.
            </p>
            @error('role')
                <p class="text-red-600 text-xs mt-1" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end gap-4 border-t border-gray-100 pt-4">
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-md hover:bg-gray-50 focus:ring-2 focus:ring-gray-500 font-medium transition">
                Anuluj
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-700 text-white rounded-md hover:bg-blue-800 focus:ring-2 focus:ring-offset-2 focus:ring-blue-700 font-bold transition shadow">
                Zapisz zmiany
            </button>
        </div>
    </form>
</div>
@endsection