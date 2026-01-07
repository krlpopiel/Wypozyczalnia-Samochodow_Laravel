<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Wypożyczalnia') }}</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        *:focus-visible {
            outline: 3px solid #2563eb;
            outline-offset: 2px;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-100 text-gray-900">
    
    <a href="#main-content" 
       class="absolute top-0 left-0 p-3 bg-blue-600 text-white -translate-y-full transition-transform focus:translate-y-0 z-50">
        Przejdź do głównej treści
    </a>

    <div class="min-h-screen flex flex-col">
        
        <nav class="bg-white border-b border-gray-100" role="navigation" aria-label="Menu główne">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <a href="{{ url('/') }}" class="text-xl font-bold text-blue-700">
                                AutoRent
                            </a>
                        </div>

                        <!-- Linki nawigacyjne -->
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <a href="{{ route('cars.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent hover:border-gray-300 text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                Oferta
                            </a>

                            @auth
                                <a href="{{ route('rentals.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent hover:border-gray-300 text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                    Moje Rezerwacje
                                </a>

                                @if(in_array(Auth::user()->role, ['admin', 'employee']))
                                    <a href="{{ route('employee.dashboard') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent hover:border-red-300 text-sm font-bold leading-5 text-red-600 hover:text-red-800 focus:outline-none focus:text-red-800 focus:border-red-300 transition duration-150 ease-in-out">
                                        Panel Pracownika
                                    </a>
                                    <a href="{{ route('employee.management') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent hover:border-purple-300 text-sm font-bold leading-5 text-purple-600 hover:text-purple-800 focus:outline-none focus:text-purple-800 focus:border-purple-300 transition duration-150 ease-in-out">
                                        Zarządzanie
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </div>

                    <!-- Wyszukiwarka i User Menu -->
                    <div class="flex items-center ml-auto">
                        <!-- Wyszukiwarka -->
                        <div class="mr-4 hidden md:block">
                            <form action="{{ route('cars.index') }}" method="GET" class="relative">
                                <input type="text" name="search" value="{{ request('search') }}" 
                                       placeholder="Szukaj samochodu..." 
                                       class="w-48 lg:w-64 pl-4 pr-10 py-1 text-sm border-gray-300 rounded-full focus:ring-blue-500 focus:border-blue-500 bg-gray-50">
                                <button type="submit" class="absolute right-0 top-0 mt-1 mr-2 text-gray-500 hover:text-blue-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </button>
                            </form>
                        </div>

                        <!-- Użytkownik / Auth -->
                        <div class="flex items-center">
                            @auth
                                <span class="mr-4 text-sm text-gray-600 hidden sm:inline">
                                    {{ Auth::user()->name }} 
                                    <span class="text-xs text-gray-400">({{ Auth::user()->role }})</span>
                                </span>
                                <form method="POST" action="{{ route('logout') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-sm text-red-600 hover:underline">Wyloguj</button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="text-sm text-gray-700 hover:text-blue-600">Logowanie</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="ml-4 text-sm text-gray-700 hover:text-blue-600">Rejestracja</a>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <h1 class="text-2xl font-bold text-gray-800">
                        {{ $header }}
                    </h1>
                </div>
            </header>
        @endif

        <main id="main-content" class="flex-grow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                @if(session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                        <p>{{ session('success') }}</p>
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>

        <footer class="bg-gray-800 text-white py-6 mt-auto">
            <div class="max-w-7xl mx-auto px-4 text-center">
                <p>&copy; {{ date('Y') }} AutoRent. Wszystkie prawa zastrzeżone.</p>
            </div>
        </footer>
    </div>
</body>
</html>