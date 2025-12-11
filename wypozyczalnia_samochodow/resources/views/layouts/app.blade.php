<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Wypożyczalnia') }}</title>

    <!-- TailwindCSS Script (w produkcji użyj vite build) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* Focus styles for accessibility */
        *:focus-visible {
            outline: 3px solid #2563eb;
            outline-offset: 2px;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-100 text-gray-900">
    
    <!-- WAŻNE: Skip Link dla nawigacji klawiaturą (A11y) -->
    <a href="#main-content" 
       class="absolute top-0 left-0 p-3 bg-blue-600 text-white -translate-y-full transition-transform focus:translate-y-0 z-50">
        Przejdź do głównej treści
    </a>

    <div class="min-h-screen flex flex-col">
        
        <!-- Nawigacja -->
        <nav class="bg-white border-b border-gray-100" role="navigation" aria-label="Menu główne">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <a href="{{ route('home') }}" class="text-xl font-bold text-blue-700">
                                AutoRent
                            </a>
                        </div>

                        <!-- Linki nawigacyjne -->
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <a href="{{ route('cars.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent hover:border-gray-300 text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                Oferta
                            </a>
                        </div>
                    </div>

                    <!-- Użytkownik / Auth -->
                    <div class="flex items-center">
                        @auth
                            <span class="mr-4 text-sm text-gray-600">Witaj, {{ Auth::user()->name }}</span>
                            <!-- Formularz wylogowania -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-sm text-red-600 hover:underline">Wyloguj</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="text-sm text-gray-700 hover:text-blue-600">Logowanie</a>
                            <a href="{{ route('register') }}" class="ml-4 text-sm text-gray-700 hover:text-blue-600">Rejestracja</a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Nagłówek strony -->
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <h1 class="text-2xl font-bold text-gray-800">
                        {{ $header }}
                    </h1>
                </div>
            </header>
        @endif

        <!-- Główna treść (Main) - atrybut id dla skip-link -->
        <main id="main-content" class="flex-grow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <!-- Flash messages -->
                @if(session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                        <p>{{ session('success') }}</p>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>

        <!-- Stopka -->
        <footer class="bg-gray-800 text-white py-6 mt-auto">
            <div class="max-w-7xl mx-auto px-4 text-center">
                <p>&copy; {{ date('Y') }} AutoRent. Aplikacja studencka.</p>
            </div>
        </footer>
    </div>
</body>
</html>