<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $team->name }} - Spēles</title>
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
                <a href="{{ route('lbs.team.overview', $team->id) }}" class="text-gray-700 hover:text-blue-600 font-medium">PĀRSKATS</a>
                <a href="{{ route('lbs.team.games', $team->id) }}" class="text-gray-700 hover:text-blue-600 font-medium font-bold">SPĒLES</a>
                <a href="{{ route('lbs.team.players', $team->id) }}" class="text-gray-700 hover:text-blue-600 font-medium">SPĒLĒTĀJI</a>
                <a href="{{ route('lbs.team.stats', $team->id) }}" class="text-gray-700 hover:text-blue-600 font-medium">STATISTIKA</a>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <main class="pt-32 max-w-7xl mx-auto px-4 space-y-8">

        <!-- Team Overall Record -->
        <section>
            <h2 class="text-2xl font-semibold text-gray-800">Komandas rezultāts</h2>
            @php
                $wins = $games->where('winner_id', $team->id)->count();
                $losses = $games->count() - $wins;
            @endphp
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

        <!-- Individual Games -->
        <section>
            <h2 class="text-2xl font-semibold text-gray-800">Spēles</h2>

            @if($games->isEmpty())
                <p class="mt-4 text-gray-500">Šai komandai vēl nav spēļu.</p>
            @else
                <div class="mt-4 space-y-4">
                    @foreach($games as $game)
                        <div class="p-4 bg-white shadow rounded-lg flex flex-col sm:flex-row justify-between items-start sm:items-center">
                            <div>
                                <p class="font-medium text-lg">
                                    {{ $game->team1->name }} vs {{ $game->team2->name }}
                                </p>
                                <p class="text-gray-600 font-bold text-xl">
                                    {{ $game->score1 }} : {{ $game->score2 }}
                                </p>
                                <p class="text-sm text-gray-500">Datums: {{ $game->date }}</p>
                            </div>
                            <div class="mt-2 sm:mt-0">
                                <a href="{{ route('lbs.game.detail', $game->id) }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                    Skatīt detalizētu statistiku
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </main>
</body>
</html>
