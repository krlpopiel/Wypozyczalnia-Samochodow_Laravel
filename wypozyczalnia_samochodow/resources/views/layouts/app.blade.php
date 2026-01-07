<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="System wypożyczalni samochodów AutoRent - przeglądaj i rezerwuj auta online.">

    <title>{{ config('app.name', 'Wypożyczalnia') }}</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        *:focus-visible {
            outline: 3px solid #2563eb;
            outline-offset: 2px;
        }
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border-width: 0;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-100 text-gray-900">

    <div class="min-h-screen flex flex-col">
        
        <nav class="bg-white border-b border-gray-100" aria-label="Menu główne">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="shrink-0 flex items-center">
                            <a href="{{ url('/') }}" class="text-xl font-bold text-blue-700 flex items-center gap-2" aria-label="AutoRent - Strona Główna">
                                <span class="text-2xl" aria-hidden="true">🚗</span> AutoRent
                            </a>
                        </div>

                        <!-- Linki nawigacyjne -->
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <a href="{{ route('cars.index') }}" 
                               class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out {{ request()->routeIs('cars.index') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                               @if(request()->routeIs('cars.index')) aria-current="page" @endif>
                                Oferta
                            </a>

                            @auth
                                <a href="{{ route('rentals.index') }}" 
                                   class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out {{ request()->routeIs('rentals.index') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                                   @if(request()->routeIs('rentals.index')) aria-current="page" @endif>
                                    Moje Rezerwacje
                                </a>

                                @if(in_array(Auth::user()->role, ['admin', 'employee']))
                                    <a href="{{ route('employee.dashboard') }}" 
                                       class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out {{ request()->routeIs('employee.dashboard') ? 'border-red-500 text-gray-900' : 'border-transparent text-red-600 hover:text-red-800 focus:text-red-800 hover:border-red-300' }}"
                                       @if(request()->routeIs('employee.dashboard')) aria-current="page" @endif>
                                        Panel Pracownika
                                    </a>
                                    <a href="{{ route('employee.management') }}" 
                                       class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out {{ request()->routeIs('employee.management') ? 'border-purple-500 text-gray-900' : 'border-transparent text-purple-600 hover:text-purple-800 focus:text-purple-800 hover:border-purple-300' }}"
                                       @if(request()->routeIs('employee.management')) aria-current="page" @endif>
                                        Zarządzanie
                                    </a>
                                @endif

                                <!-- NOWY LINK TYLKO DLA ADMINA -->
                                @if(Auth::user()->role === 'admin')
                                    <a href="{{ route('admin.users.index') }}" 
                                       class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out {{ request()->routeIs('admin.users.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-indigo-600 hover:text-indigo-800 focus:text-indigo-800 hover:border-indigo-300' }}"
                                       @if(request()->routeIs('admin.users.*')) aria-current="page" @endif>
                                        Użytkownicy
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </div>

                    <div class="flex items-center ml-auto">
                        <!-- Wyszukiwarka -->
                        <div class="mr-4 hidden md:block" role="search">
                            <form action="{{ route('cars.index') }}" method="GET" class="relative">
                                <label for="global-search" class="sr-only">Szukaj samochodu</label>
                                <input type="text" id="global-search" name="search" value="{{ request('search') }}" 
                                       placeholder="Szukaj samochodu..." 
                                       class="w-48 lg:w-64 pl-4 pr-10 py-1 text-sm border-gray-300 rounded-full focus:ring-blue-500 focus:border-blue-500 bg-gray-50 text-gray-800 placeholder-gray-500">
                                <button type="submit" class="absolute right-0 top-0 mt-1 mr-2 text-gray-500 hover:text-blue-600" aria-label="Szukaj">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </button>
                            </form>
                        </div>

                        <!-- Użytkownik -->
                        <div class="flex items-center">
                            @auth
                                <span class="mr-4 text-sm text-gray-700 hidden sm:inline font-medium">
                                    {{ Auth::user()->name }} 
                                    <span class="text-xs text-gray-500">({{ Auth::user()->role }})</span>
                                </span>
                                <form method="POST" action="{{ route('logout') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-sm text-red-700 hover:underline font-medium focus:text-red-800">Wyloguj</button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="text-sm text-gray-700 hover:text-blue-700 font-medium p-2">Logowanie</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="ml-2 text-sm text-gray-700 hover:text-blue-700 font-medium p-2">Rejestracja</a>
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
                    <div class="bg-green-50 border-l-4 border-green-600 text-green-800 p-4 mb-6" role="alert">
                        <p class="font-medium">{{ session('success') }}</p>
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-600 text-red-800 p-4 mb-6" role="alert">
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

        <footer class="bg-gray-900 text-white py-8 mt-auto border-t-4 border-blue-600">
            <div class="max-w-7xl mx-auto px-4 text-center">
                <p class="text-gray-300">&copy; {{ date('Y') }}  AutoRent - Adrian Popielarczyk 2026</p>
            </div>
        </footer>
    </div>
    @assistForWCAG
</body>
</html>