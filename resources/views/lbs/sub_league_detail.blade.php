<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subLeague->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const menuBtn = document.getElementById('menu-btn');
            const mobileMenu = document.getElementById('mobile-menu');
            menuBtn.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        });
    </script>
</head>
<body class="bg-gray-100">

<!-- Main Navbar -->
<nav class="bg-white shadow-md fixed w-full top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <div class="flex items-center space-x-4">
                <a href="{{ route('home') }}" class="flex items-center">
                    <img src="{{ asset('home-icon-silhouette-svgrepo-com.svg') }}" 
                         alt="Home" class="h-8 w-8 hover:opacity-80">
                </a>
                <a href="{{ route('lbs.home') }}" class="flex items-center">
                    <img src="{{ asset('415986933_1338154883529529_7481933183149808416_n.jpg') }}" 
                         alt="LBS Logo" class="h-10 w-auto">
                </a>
            </div>

            <!-- Desktop Parent Leagues -->
            <div class="hidden md:flex space-x-6">
                @isset($parentLeagues)
                    @foreach($parentLeagues as $league)
                        <a href="{{ route('lbs.league.show', $league->id) }}"
                           class="text-gray-700 hover:text-blue-600 font-medium">
                            {{ $league->name }}
                        </a>
                    @endforeach
                @endisset
            </div>

            <!-- Mobile Menu Button -->
            <div class="md:hidden flex items-center">
                <button id="menu-btn" class="focus:outline-none">
                    <img src="{{ asset('burger-menu-svgrepo-com.svg') }}" alt="Menu" class="h-8 w-8">
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Parent League Menu -->
    <div id="mobile-menu" class="hidden md:hidden bg-white shadow-lg">
        <div class="space-y-2 px-4 py-3">
            @isset($parentLeagues)
                @foreach($parentLeagues as $league)
                    <a href="{{ route('lbs.league.show', $league->id) }}" 
                       class="block text-gray-700 hover:text-blue-600">
                        {{ $league->name }}
                    </a>
                @endforeach
            @endisset
        </div>
    </div>
</nav>

<!-- Sub-League Secondary Navbar -->
<nav class="bg-gray-50 shadow-inner fixed top-16 w-full z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex space-x-6 py-3">
            <a href="{{ route('lbs.subleague.news', $subLeague->id) }}" 
               class="text-gray-700 hover:text-blue-600 font-medium {{ request()->routeIs('lbs.subleague.news') ? 'text-blue-600 font-bold' : '' }}">
                JAUNUMI
            </a>

            <a href="{{ route('lbs.subleague.calendar', $subLeague->id) }}" 
               class="text-gray-700 hover:text-blue-600 font-medium {{ request()->routeIs('lbs.subleague.calendar') ? 'text-blue-600 font-bold' : '' }}">
                KALENDĀRS
            </a>

            <a href="{{ route('lbs.subleague.teams', $subLeague->id) }}" 
               class="text-gray-700 hover:text-blue-600 font-medium {{ request()->routeIs('lbs.subleague.teams') ? 'text-blue-600 font-bold' : '' }}">
                KOMANDAS
            </a>

            <a href="{{ route('lbs.subleague.stats', $subLeague->id) }}" 
               class="text-gray-700 hover:text-blue-600 font-medium {{ request()->routeIs('lbs.subleague.stats') ? 'text-blue-600 font-bold' : '' }}">
                STATISTIKA
            </a>
        </div>
    </div>
</nav>

<!-- Page Content -->
<main class="pt-32 max-w-7xl mx-auto px-4 space-y-6">

    <h1 class="text-3xl font-bold text-gray-800">{{ $subLeague->name }}</h1>
    
    {{-- Page-specific content --}}
    @yield('subleague-content')

</main>

</body>
</html>
