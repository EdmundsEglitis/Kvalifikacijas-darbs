<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subLeague->name }} - Kalendārs</title>
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

                <!-- Desktop Parent Leagues -->
                <div class="hidden md:flex space-x-6">
                    @foreach($parentLeagues as $league)
                        <a href="{{ route('lbs.league.show', $league->id) }}"
                           class="text-gray-700 hover:text-blue-600 font-medium">
                            {{ $league->name }}
                        </a>
                    @endforeach
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
                @foreach($parentLeagues as $league)
                    <a href="{{ route('lbs.league.show', $league->id) }}" 
                       class="block text-gray-700 hover:text-blue-600">
                        {{ $league->name }}
                    </a>
                @endforeach
            </div>
        </div>
    </nav>

    <!-- Sub-League Tabs Navbar -->
    <nav class="bg-gray-50 shadow-inner fixed top-16 w-full z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex space-x-6 py-3">
                <a href="{{ route('lbs.subleague.show', $subLeague->id) }}" 
                   class="text-gray-700 hover:text-blue-600 font-medium">
                   JAUNUMI
                </a>
                <a href="{{ route('lbs.subleague.calendar', $subLeague->id) }}" 
                   class="text-blue-600 font-bold">
                   KALENDĀRS
                </a>
                <a href="{{ route('lbs.subleague.teams', $subLeague->id) }}" 
                   class="text-gray-700 hover:text-blue-600 font-medium">
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
    <main class="pt-32 max-w-4xl mx-auto px-4">
        <h1 class="text-3xl font-bold text-gray-800">{{ $subLeague->name }} - Kalendārs</h1>

        @if($games->isEmpty())
            <p class="mt-4 text-gray-500">Nav pieejamu spēļu.</p>
        @else
            <div class="mt-6 space-y-6">
                @foreach($games as $game)
                    <div class="bg-white shadow-md rounded-lg p-6 border border-gray-200 flex flex-col items-center">
                        <div class="text-xl font-semibold text-gray-800">
                            {{ $game->team1->name }} 
                            <span class="text-gray-500">vs</span> 
                            {{ $game->team2->name }}
                        </div>
                        <div class="text-3xl font-bold text-blue-600 mt-3">
                            {{ $game->score ?? '—' }}
                        </div>
                        <div class="text-sm text-gray-500 mt-2">
                            {{ $game->date }}
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </main>
</body>
</html>
