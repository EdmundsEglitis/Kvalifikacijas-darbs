<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $parent->name }} - Sub Leagues</title>
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
    <!-- Navbar (same as lbs.home) -->
    <nav class="bg-white shadow-md fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <!-- Logo + Home -->
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
                    @foreach(\App\Models\League::whereNull('parent_id')->get() as $league)
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

        <!-- Mobile Nav -->
        <div id="mobile-menu" class="hidden md:hidden bg-white shadow-lg">
            <div class="space-y-2 px-4 py-3">
                @foreach(\App\Models\League::whereNull('parent_id')->get() as $league)
                    <a href="{{ route('lbs.league.show', $league->id) }}" 
                       class="block text-gray-700 hover:text-blue-600">
                        {{ $league->name }}
                    </a>
                @endforeach
            </div>
        </div>
    </nav>

    <!-- Page content -->
    <main class="pt-20 max-w-7xl mx-auto px-4">
        <h1 class="text-3xl font-bold text-gray-800">{{ $parent->name }} - Sub-Leagues</h1>
        <p class="mt-4 text-gray-600">Izvēlieties apakšlīgu:</p>

        @if($subLeagues->isEmpty())
            <p class="text-gray-500 mt-4">Nav atrasta neviena apakšlīga.</p>
        @else
        <ul class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mt-4">
    @foreach($subLeagues as $sub)
        <li>
            <a href="{{ route('lbs.subleague.news', $sub->id) }}"
               class="block p-4 rounded-lg shadow hover:bg-gray-100 transition text-center font-medium">
                {{ $sub->name }}
            </a>
        </li>
    @endforeach
</ul>
        @endif
    </main>
</body>
</html>
