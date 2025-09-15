<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subLeague->name }} - Komandas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const menuBtn = document.getElementById('menu-btn');
            const mobileMenu = document.getElementById('mobile-menu');
            if (menuBtn) {
                menuBtn.addEventListener('click', () => {
                    mobileMenu.classList.toggle('hidden');
                });
            }
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

            <!-- Desktop Nav -->
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

    <!-- Mobile Nav -->
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

<!-- Sub-League Tabs Navbar -->
<nav class="bg-gray-50 shadow-inner fixed top-16 w-full z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex space-x-6 py-3">
        <a href="{{ route('lbs.subleague.news', $subLeague->id) }}" 
               class="text-gray-700 hover:text-blue-600 font-medium {{ request()->routeIs('lbs.subleague') ? 'text-blue-600 font-bold' : '' }}">
                JAUNUMI
            </a>
            <a href="{{ route('lbs.subleague.calendar', $subLeague->id) }}" 
               class="text-gray-700 hover:text-blue-600 font-medium">
               KALENDĀRS
            </a>
            <a href="{{ route('lbs.subleague.teams', $subLeague->id) }}" 
               class="text-blue-600 font-bold">
               KOMANDAS
            </a>
            <a href="{{ route('lbs.subleague.stats', $subLeague->id) }}" 
               class="text-gray-700 hover:text-blue-600 font-medium">
               STATISTIKA
            </a>
        </div>
    </div>
</nav>

<!-- Page Content -->
<main class="pt-32 max-w-6xl mx-auto px-4 space-y-8">

    <h1 class="text-2xl font-bold text-gray-800">{{ $subLeague->name }} - Komandas</h1>

    @if($teams->isEmpty())
        <p class="mt-4 text-gray-500">Šai līgai vēl nav komandu.</p>
    @else
        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($teams as $team)
                <div class="bg-white shadow rounded-lg p-4 flex flex-col justify-between">
                    <h2 class="text-lg font-semibold text-gray-800">{{ $team->name }}</h2>
                    @if($team->logo)
                        <img src="{{ asset('storage/' . $team->logo) }}" alt="{{ $team->name }}" class="mt-2 h-24 w-auto object-contain">
                    @endif
                    <a href="{{ route('lbs.team.overview', $team->id) }}" 
                       class="mt-4 text-center bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
                        Skatīt komandu
                    </a>
                </div>
            @endforeach
        </div>
    @endif

</main>

</body>
</html>
