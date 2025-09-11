<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $game->team1->name }} vs {{ $game->team2->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

    <!-- Main Navbar -->
    <nav class="bg-white shadow-md fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('home-icon-silhouette-svgrepo-com.svg') }}" class="h-8 w-8 hover:opacity-80">
                    </a>
                    <a href="{{ route('lbs.home') }}">
                        <img src="{{ asset('415986933_1338154883529529_7481933183149808416_n.jpg') }}" class="h-10 w-auto">
                    </a>
                </div>

                <!-- Desktop Nav -->
                <div class="hidden md:flex space-x-6">
                    @foreach(\App\Models\League::whereNull('parent_id')->get() as $league)
                        <a href="{{ route('lbs.league.show', $league->id) }}" class="text-gray-700 hover:text-blue-600 font-medium">
                            {{ $league->name }}
                        </a>
                    @endforeach
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center">
                    <button id="menu-btn" class="focus:outline-none" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                        <img src="{{ asset('burger-menu-svgrepo-com.svg') }}" alt="Menu" class="h-8 w-8">
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-white shadow-lg">
            <div class="space-y-2 px-4 py-3">
                @foreach(\App\Models\League::whereNull('parent_id')->get() as $league)
                    <a href="{{ route('lbs.league.show', $league->id) }}" class="block text-gray-700 hover:text-blue-600">
                        {{ $league->name }}
                    </a>
                @endforeach
            </div>
        </div>
    </nav>

    <!-- Game Context Navbar -->
    <nav class="bg-gray-50 shadow-inner fixed top-16 w-full z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex space-x-6 py-3">
                <a href="{{ route('lbs.team.overview', $game->team1->id) }}" class="text-gray-700 hover:text-blue-600 font-medium">
                    {{ $game->team1->name }} PĀRSKATS
                </a>
                <a href="{{ route('lbs.team.overview', $game->team2->id) }}" class="text-gray-700 hover:text-blue-600 font-medium">
                    {{ $game->team2->name }} PĀRSKATS
                </a>
            </div>
        </div>
    </nav>

    <main class="pt-32 max-w-6xl mx-auto px-4 space-y-6">

        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-2xl font-bold mb-2">{{ $game->team1->name }} vs {{ $game->team2->name }}</h2>
            <p class="text-gray-600 mb-2">Datums: {{ $game->date }}</p>
            <p class="text-xl font-semibold mb-4">Final Score: {{ $team1Score }} : {{ $team2Score }}</p>

            @if($game->team1_q1 !== null)
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="bg-gray-100 p-3 rounded">
                    <h3 class="font-semibold mb-2">{{ $game->team1->name }} Puslaiku punkti</h3>
                    <p>Q1: {{ $game->team1_q1 }}</p>
                    <p>Q2: {{ $game->team1_q2 }}</p>
                    <p>Q3: {{ $game->team1_q3 }}</p>
                    <p>Q4: {{ $game->team1_q4 }}</p>
                </div>
                <div class="bg-gray-100 p-3 rounded">
                    <h3 class="font-semibold mb-2">{{ $game->team2->name }} Puslaiku punkti</h3>
                    <p>Q1: {{ $game->team2_q1 }}</p>
                    <p>Q2: {{ $game->team2_q2 }}</p>
                    <p>Q3: {{ $game->team2_q3 }}</p>
                    <p>Q4: {{ $game->team2_q4 }}</p>
                </div>
            </div>
            @endif

            <div class="grid grid-cols-2 gap-6">
                @foreach([$game->team1_id, $game->team2_id] as $teamId)
                    @php
                        $team = $teamId == $game->team1_id ? $game->team1 : $game->team2;
                        $stats = $playerStats[$teamId] ?? collect();
                        $totals = [
                            'points' => $stats->sum('points'),
                            'reb' => $stats->sum('reb'),
                            'ast' => $stats->sum('ast'),
                            'eff' => $stats->sum('eff'),
                        ];
                    @endphp
                    <div class="bg-white shadow rounded-lg p-4">
                        <h2 class="text-xl font-bold mb-2">{{ $team->name }} Spēlētāju statistika</h2>
                        <table class="min-w-full table-auto border-collapse border border-gray-300">
                            <thead>
                                <tr class="bg-gray-200">
                                    <th class="px-2 py-1 border">Spēlētājs</th>
                                    <th class="px-2 py-1 border">PTS</th>
                                    <th class="px-2 py-1 border">REB</th>
                                    <th class="px-2 py-1 border">AST</th>
                                    <th class="px-2 py-1 border">EFF</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stats as $stat)
                                <tr>
                                    <td class="px-2 py-1 border">{{ $stat->player->name }}</td>
                                    <td class="px-2 py-1 border">{{ $stat->points }}</td>
                                    <td class="px-2 py-1 border">{{ $stat->reb }}</td>
                                    <td class="px-2 py-1 border">{{ $stat->ast }}</td>
                                    <td class="px-2 py-1 border">{{ $stat->eff }}</td>
                                </tr>
                                @endforeach
                                <tr class="font-bold bg-gray-100">
                                    <td class="px-2 py-1 border">Kopā</td>
                                    <td class="px-2 py-1 border">{{ $totals['points'] }}</td>
                                    <td class="px-2 py-1 border">{{ $totals['reb'] }}</td>
                                    <td class="px-2 py-1 border">{{ $totals['ast'] }}</td>
                                    <td class="px-2 py-1 border">{{ $totals['eff'] }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @endforeach
            </div>

        </div>
    </main>

</body>
</html>
