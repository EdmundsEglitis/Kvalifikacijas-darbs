<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $team->name }} - Komandas pārskats</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

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

        <!-- Mobile Nav -->
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


    <!-- Team Navbar -->
    <nav class="bg-gray-50 shadow-inner fixed top-16 w-full z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex space-x-6 py-3">
                <a href="{{ route('lbs.team.overview', $team->id) }}" 
                   class="text-gray-700 hover:text-blue-600 font-medium {{ request()->routeIs('lbs.team.overview') ? 'text-blue-600 font-bold' : '' }}">
                    PĀRSKATS
                </a>
                <a href="{{ route('lbs.team.games', $team->id) }}" 
                   class="text-gray-700 hover:text-blue-600 font-medium {{ request()->routeIs('lbs.team.games') ? 'text-blue-600 font-bold' : '' }}">
                    SPĒLES
                </a>
                <a href="{{ route('lbs.team.players', $team->id) }}" 
                   class="text-gray-700 hover:text-blue-600 font-medium {{ request()->routeIs('lbs.team.players') ? 'text-blue-600 font-bold' : '' }}">
                    SPĒLĒTĀJI
                </a>
                <a href="{{ route('lbs.team.stats', $team->id) }}" 
                   class="text-gray-700 hover:text-blue-600 font-medium {{ request()->routeIs('lbs.team.stats') ? 'text-blue-600 font-bold' : '' }}">
                    STATISTIKA
                </a>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <main class="pt-32 max-w-6xl mx-auto px-4 space-y-10">


    <!-- Team Record -->
<section>
    <h2 class="text-2xl font-semibold text-gray-800">Komandas rezultāts</h2>
    <div class="mt-4 flex space-x-6">
        <div class="p-4 bg-white shadow rounded-lg text-center w-32">
            <p class="text-lg font-bold">{{ $wins }}</p>
            <p class="text-sm text-gray-600">Uzvaras</p>
        </div>
        <div class="p-4 bg-white shadow rounded-lg text-center w-32">
            <p class="text-lg font-bold">{{ $losses }}</p>
            <p class="text-sm text-gray-600">Zaudējumi</p>
        </div>
    </div>
</section>



        <!-- Average Team Stats -->
        <section>
            <h2 class="text-2xl font-semibold text-gray-800">Vidējie komandas statistikas rādītāji</h2>

            @if(empty($averageStats))
                <p class="mt-2 text-gray-500">Nav pieejamas statistikas.</p>
            @else
                <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
                    <div class="p-4 bg-white shadow rounded-lg text-center">
                        <p class="text-lg font-bold">{{ number_format($averageStats['points'], 1) }}</p>
                        <p class="text-sm text-gray-600">Punkti</p>
                    </div>
                    <div class="p-4 bg-white shadow rounded-lg text-center">
                        <p class="text-lg font-bold">{{ number_format($averageStats['reb'], 1) }}</p>
                        <p class="text-sm text-gray-600">Atlēkušās bumbas</p>
                    </div>
                    <div class="p-4 bg-white shadow rounded-lg text-center">
                        <p class="text-lg font-bold">{{ number_format($averageStats['ast'], 1) }}</p>
                        <p class="text-sm text-gray-600">Piespēles</p>
                    </div>
                    <div class="p-4 bg-white shadow rounded-lg text-center">
                        <p class="text-lg font-bold">{{ number_format($averageStats['stl'], 1) }}</p>
                        <p class="text-sm text-gray-600">Pārķertās</p>
                    </div>
                    <div class="p-4 bg-white shadow rounded-lg text-center">
                        <p class="text-lg font-bold">{{ number_format($averageStats['blk'], 1) }}</p>
                        <p class="text-sm text-gray-600">Bloki</p>
                    </div>
                </div>
            @endif
        </section>

        <!-- Best Players -->
        <section>
            <h2 class="text-2xl font-semibold text-gray-800">Labākie spēlētāji</h2>
            
            @if(empty($bestPlayers) || collect($bestPlayers)->every(fn($player) => is_null($player)))
                <p class="mt-2 text-gray-500">Nav pieejamu spēlētāju datu.</p>
            @else
                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                    @foreach($bestPlayers as $stat => $player)
                        @if($player)
                            <div class="p-4 bg-white shadow rounded-lg">
                                <h3 class="text-lg font-medium text-gray-700">{{ ucfirst($stat) }} līderis</h3>
                                <p class="mt-2 font-semibold text-gray-800">{{ $player['name'] }}</p>
                                <p class="text-gray-600">{{ $player['value'] }} {{ $stat }}</p>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </section>

    </main>
</body>
</html>
