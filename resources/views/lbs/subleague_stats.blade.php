<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subLeague->name }} - Statistika</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const menuBtn = document.getElementById('menu-btn');
            const mobileMenu = document.getElementById('mobile-menu');
            if(menuBtn){
                menuBtn.addEventListener('click', () => {
                    mobileMenu.classList.toggle('hidden');
                });
            }

            // Simple sorting for All Players table
            document.querySelectorAll('[data-sort]').forEach(header => {
                header.addEventListener('click', () => {
                    const table = header.closest('table');
                    const rows = Array.from(table.querySelector('tbody').rows);
                    const idx = header.cellIndex;
                    const asc = header.dataset.asc === 'true' ? false : true;
                    header.dataset.asc = asc;

                    rows.sort((a, b) => {
                        let v1 = a.cells[idx].innerText;
                        let v2 = b.cells[idx].innerText;

                        if(!isNaN(v1) && !isNaN(v2)) {
                            v1 = parseFloat(v1); v2 = parseFloat(v2);
                        }

                        return asc ? v1 > v2 ? 1 : -1 : v1 < v2 ? 1 : -1;
                    });

                    table.querySelector('tbody').append(...rows);
                });
            });

            // Search filter for All Players table
            const searchInput = document.getElementById('player-search');
            if (searchInput) {
                searchInput.addEventListener('input', (e) => {
                    const term = e.target.value.toLowerCase();
                    document.querySelectorAll('#players-table tbody tr').forEach(row => {
                        row.style.display = row.innerText.toLowerCase().includes(term) ? '' : 'none';
                    });
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
            <a href="{{ route('lbs.subleague.news', $subLeague->id) }}" 
               class="text-gray-700 hover:text-blue-600 font-medium {{ request()->routeIs('lbs.subleague') ? 'text-blue-600 font-bold' : '' }}">
                JAUNUMI
            </a>

                <a href="{{ route('lbs.subleague.calendar', $subLeague->id) }}" 
                   class="text-gray-700 hover:text-blue-600 font-medium">KALENDĀRS</a>

                <a href="{{ route('lbs.subleague.teams', $subLeague->id) }}" 
                   class="text-gray-700 hover:text-blue-600 font-medium">KOMANDAS</a>

                <a href="{{ route('lbs.subleague.stats', $subLeague->id) }}" 
                   class="text-blue-600 font-bold border-b-2 border-blue-600">STATISTIKA</a>
            </div>
        </div>
    </nav>

    <!-- Stats Section Navbar -->
    <nav class="bg-white fixed top-28 w-full z-30 shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex space-x-6 py-2">
                <a href="#teams" class="text-blue-600 font-bold">Komandu statistika</a>
                <a href="#top-players" class="text-gray-700 hover:text-blue-600">Top spēlētāji</a>
                <a href="#all-players" class="text-gray-700 hover:text-blue-600">Spēlētāji</a>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <main class="pt-44 max-w-7xl mx-auto px-4 space-y-16">

        <!-- Team Stats -->
        <section id="teams">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">{{ $subLeague->name }} - Komandu statistika</h1>

            @php
                $sortedTeams = $teamsStats->sortByDesc('wins');
            @endphp

            @foreach($sortedTeams as $teamStat)
                <div class="bg-white shadow rounded-lg p-6 mb-4">
                    <h2 class="text-xl font-semibold text-gray-800">{{ $teamStat['team']->name }}</h2>
                    <p class="mt-2 text-green-600 font-bold">Uzvaras: {{ $teamStat['wins'] }}</p>
                    <p class="text-red-600 font-bold">Zaudējumi: {{ $teamStat['losses'] }}</p>
                </div>
            @endforeach
        </section>

        <!-- Top Players -->
        <section id="top-players">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Top spēlētāji</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($topPlayers as $stat => $player)
                    <div class="bg-white shadow rounded-lg p-6 text-center">
                        <h3 class="text-lg font-semibold capitalize">{{ $stat }}</h3>
                        <p class="mt-2 text-gray-800 font-bold">{{ $player->name }}</p>
                        <p class="text-blue-600">Vidēji: {{ $player->avg_value }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- All Players -->
        <section id="all-players">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Visi spēlētāji</h2>
            <input id="player-search" type="text" placeholder="Meklēt spēlētāju..." 
                   class="mb-4 w-full border rounded px-3 py-2">

            <div class="overflow-x-auto">
                <table id="players-table" class="min-w-full bg-white shadow rounded-lg">
                    <thead>
                        <tr class="bg-gray-200">
                            <th data-sort class="px-4 py-2 cursor-pointer">Spēlētājs</th>
                            <th data-sort class="px-4 py-2 cursor-pointer">Komanda</th>
                            <th data-sort class="px-4 py-2 cursor-pointer">Punkti AVG</th>
                            <th data-sort class="px-4 py-2 cursor-pointer">Atlēkušās AVG</th>
                            <th data-sort class="px-4 py-2 cursor-pointer">Rezultīvās piespēles AVG</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($playersStats as $player)
                            <tr class="border-t">
                                <td class="px-4 py-2">{{ $player->name }}</td>
                                <td class="px-4 py-2">{{ $player->team->name }}</td>
                                <td class="px-4 py-2">{{ $player->avg_points }}</td>
                                <td class="px-4 py-2">{{ $player->avg_rebounds }}</td>
                                <td class="px-4 py-2">{{ $player->avg_assists }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

    </main>

</body>
</html>
