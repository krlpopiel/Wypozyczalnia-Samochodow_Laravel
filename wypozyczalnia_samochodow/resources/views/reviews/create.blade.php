@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow-md border border-gray-200">
    <div class="mb-6 border-b border-gray-200 pb-4">
        <h1 class="text-2xl font-bold text-gray-800">Wystaw opinię</h1>
        <p class="text-sm text-gray-600 mt-1">
            Samochód: <strong>{{ $rental->car->brand->name }} {{ $rental->car->model }}</strong> 
            (<span class="font-mono text-xs">{{ $rental->car->registration_plate }}</span>)
        </p>
    </div>

    <form action="{{ route('reviews.store', $rental) }}" method="POST">
        @csrf

        <!-- Ocena (Gwiazdki) -->
        <fieldset class="mb-6">
            <legend class="block text-sm font-bold text-gray-700 mb-2">Twoja ocena (1-5 gwiazdek)</legend>
            <div class="flex gap-4 items-center">
                @for($i = 5; $i >= 1; $i--)
                    <div class="flex items-center">
                        <input type="radio" id="rating_{{ $i }}" name="rating" value="{{ $i }}" class="w-4 h-4 text-yellow-500 border-gray-300 focus:ring-yellow-500" required>
                        <label for="rating_{{ $i }}" class="ml-1 text-sm text-gray-700">{{ $i }} <span class="sr-only">Gwiazdek</span></label>
                    </div>
                @endfor
            </div>
            @error('rating')
                <p class="text-red-600 text-xs mt-1" role="alert">{{ $message }}</p>
            @enderror
        </fieldset>

        <!-- Komentarz -->
        <div class="mb-6">
            <label for="comment" class="block text-sm font-bold text-gray-700 mb-2">Komentarz (opcjonalnie)</label>
            <textarea name="comment" id="comment" rows="4" 
                      class="w-full text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                      placeholder="Co Ci się podobało? Czy samochód był czysty? Jak oceniasz komfort jazdy?"></textarea>
            @error('comment')
                <p class="text-red-600 text-xs mt-1" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('rentals.index') }}" class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-md hover:bg-gray-50 focus:ring-2 focus:ring-gray-500 font-medium transition">
                Anuluj
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-700 text-white rounded-md hover:bg-blue-800 focus:ring-2 focus:ring-offset-2 focus:ring-blue-700 font-bold transition shadow">
                Wyślij opinię
            </button>
        </div>
    </form>
</div>
@endsection