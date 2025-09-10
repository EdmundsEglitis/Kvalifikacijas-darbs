<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $team->name }} - Komandas statistika</title>
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
                <a href="{{ route('lbs.team.games', $team->id) }}" class="text-gray-700 hover:text-blue-600 font-medium">SPĒLES</a>
                <a href="{{ route('lbs.team.players', $team->id) }}" class="text-gray-700 hover:text-blue-600 font-medium">SPĒLĒTĀJI</a>
                <a href="{{ route('lbs.team.stats', $team->id) }}" class="text-gray-700 hover:text-blue-600 font-medium font-bold">STATISTIKA</a>
            </div>
        </div>
    </nav>

    <main class="pt-32 max-w-7xl mx-auto px-4 space-y-10">

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

        <!-- Team Average Stats -->
        <section>
            <h2 class="text-2xl font-semibold text-gray-800">Vidējie rādītāji vidējā spēlē</h2>
            <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
                @php
                    $totalGames = $games->count() ?: 1; // avoid division by zero
                    $stats = ['points'=>'Punkti', 'oreb'=>'Atl. bumbas uzbrukumā', 'dreb'=>'Atl. bumbas aizsardzībā', 'reb'=>'Atl. bumbas', 'ast'=>'Piespēles', 'pf'=>'Fouls', 'tov'=>'Kļūdas', 'stl'=>'Pārķertās', 'blk'=>'Bloķētie metieni', 'dunk'=>'Danki'];
                @endphp

                @foreach($stats as $key => $label)
                    @php
                        $total = $team->players->sum(fn($p) => $p->games->sum("pivot.$key"));
                        $avg = $total / $totalGames;
                    @endphp
                    <div class="p-4 bg-white shadow rounded-lg text-center">
                        <p class="text-lg font-bold">{{ number_format($avg, 1) }}</p>
                        <p class="text-sm text-gray-600">{{ $label }} vid. spēlē</p>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- Player Stats Table -->
        <section>
            <h2 class="text-2xl font-semibold text-gray-800">Spēlētāju statistika</h2>
            <div class="overflow-x-auto mt-4 bg-white shadow rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Vārds</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">PPG</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">G</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">MPG</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">ORPG</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">DRPG</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">RPG</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">APG</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">FPG</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">ToPG</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">SPG</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">BSG</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">DPG</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($team->players as $player)
                            @php
                                $gamesPlayed = $player->games->count() ?: 1;
                            @endphp
                            <tr>
                                <td class="px-4 py-2">{{ $player->name }}</td>
                                <td class="px-4 py-2 text-right">{{ number_format($player->games->sum('pivot.points') / $gamesPlayed, 1) }}</td>
                                <td class="px-4 py-2 text-right">{{ $gamesPlayed }}</td>
                                <td class="px-4 py-2 text-right">{{ gmdate('i:s', intval($player->games->sum(fn($g) => strtotime($g->pivot->minutes) - strtotime('00:00')) / $gamesPlayed)) }}</td>
                                <td class="px-4 py-2 text-right">{{ number_format($player->games->sum('pivot.oreb') / $gamesPlayed, 1) }}</td>
                                <td class="px-4 py-2 text-right">{{ number_format($player->games->sum('pivot.dreb') / $gamesPlayed, 1) }}</td>
                                <td class="px-4 py-2 text-right">{{ number_format($player->games->sum('pivot.reb') / $gamesPlayed, 1) }}</td>
                                <td class="px-4 py-2 text-right">{{ number_format($player->games->sum('pivot.ast') / $gamesPlayed, 1) }}</td>
                                <td class="px-4 py-2 text-right">{{ number_format($player->games->sum('pivot.pf') / $gamesPlayed, 1) }}</td>
                                <td class="px-4 py-2 text-right">{{ number_format($player->games->sum('pivot.tov') / $gamesPlayed, 1) }}</td>
                                <td class="px-4 py-2 text-right">{{ number_format($player->games->sum('pivot.stl') / $gamesPlayed, 1) }}</td>
                                <td class="px-4 py-2 text-right">{{ number_format($player->games->sum('pivot.blk') / $gamesPlayed, 1) }}</td>
                                <td class="px-4 py-2 text-right">{{ number_format($player->games->sum('pivot.dunk') / $gamesPlayed, 1) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>
