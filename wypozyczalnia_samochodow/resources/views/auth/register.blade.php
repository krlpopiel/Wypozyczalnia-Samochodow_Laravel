@extends('layouts.app')

@section('content')
<div class="flex justify-center items-center min-h-[50vh]">
    <div class="w-full max-w-md bg-white rounded-lg shadow-md p-8">
        <h2 class="text-2xl font-bold text-center mb-6 text-gray-800">Rejestracja</h2>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Imię i Nazwisko -->
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Imię i Nazwisko</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2 border">
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Adres Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2 border">
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Hasło -->
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Hasło</label>
                <input type="password" name="password" id="password" required
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2 border">
                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Potwierdzenie Hasła -->
            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Potwierdź hasło</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2 border">
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                Zarejestruj się
            </button>
        </form>

        <p class="mt-4 text-center text-sm text-gray-600">
            Masz już konto? <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Zaloguj się</a>
        </p>
    </div>
</div>
@endsection