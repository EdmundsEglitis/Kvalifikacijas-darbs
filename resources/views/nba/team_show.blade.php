<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>{{ $team->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="antialiased text-[#F3F4F6] bg-[#111827]">
    <!-- NAVBAR -->
    <nav class="fixed inset-x-0 top-0 z-50 bg-[#111827]/90 backdrop-blur-md">
        <div class="max-w-7xl mx-auto flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center space-x-3">
                <a href="{{ route('home') }}">
                    <img src="{{ asset('home-icon-silhouette-svgrepo-com.svg') }}"
                         alt="Home" class="h-8 w-8 filter invert"/>
                </a>
                <a href="{{ route('nba.home') }}" class="text-white font-bold hover:text-[#84CC16]">NBA</a>
            </div>
            <div class="flex items-center space-x-6 text-sm font-medium">
                <a href="{{ route('nba.players') }}" class="hover:text-[#84CC16]">Players</a>
                <a href="{{ route('nba.games.upcoming') }}" class="hover:text-[#84CC16]">Upcoming Games</a>
                <a href="{{ route('nba.games.all') }}" class="hover:text-[#84CC16]">All Games</a>
                <a href="{{ route('nba.teams') }}" class="hover:text-[#84CC16]">Teams</a>
                <a href="{{ route('nba.stats') }}" class="hover:text-[#84CC16]">Stats</a>
            </div>
        </div>
    </nav>

    <main class="pt-24 max-w-7xl mx-auto px-4 space-y-10">

        <!-- TEAM HEADER -->
        <div class="bg-[#1f2937] rounded-xl p-6 flex items-center space-x-6">
            @if($team->logo)
                <img src="{{ $team->logo }}" alt="{{ $team->name }}" class="h-20 w-20 object-contain">
            @endif
            <div>
                <h1 class="text-3xl font-bold text-white">{{ $team->name }}</h1>
                <p class="text-gray-400">Abbreviation: {{ $team->abbreviation ?? '-' }}</p>
                @if($team->url)
                    <a href="{{ $team->url }}" target="_blank"
                       class="text-[#84CC16] hover:underline">View on ESPN</a>
                @endif
            </div>
        </div>

        <!-- ROSTER -->
        <div>
            <h2 class="text-2xl font-semibold mb-4 text-white">Roster</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6">
                @forelse($players as $player)
                    <a href="{{ route('nba.player.show', $player->external_id) }}"
                       class="bg-[#1f2937] rounded-lg p-4 flex flex-col items-center hover:bg-[#374151] transition">
                        @if($player->image)
                            <img src="{{ $player->image }}" alt="{{ $player->full_name }}"
                                 class="h-16 w-16 rounded-full mb-2 object-cover ring-2 ring-[#84CC16]">
                        @else
                            <div class="h-16 w-16 rounded-full mb-2 flex items-center justify-center bg-gray-700 text-gray-400 text-xs">
                                No Photo
                            </div>
                        @endif
                        <h3 class="text-sm font-semibold text-gray-200 text-center">{{ $player->full_name }}</h3>
                    </a>
                @empty
                    <p class="col-span-full text-gray-400">No players found.</p>
                @endforelse
            </div>
        </div>

        <!-- UPCOMING GAMES -->
        <div>
            <h2 class="text-2xl font-semibold mb-4 text-white">Upcoming Games</h2>
            <div class="overflow-x-auto bg-[#1f2937] rounded-lg">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-[#111827] border-b border-[#374151] text-gray-400">
                        <tr>
                            <th class="px-4 py-2">Date</th>
                            <th class="px-4 py-2">Home</th>
                            <th class="px-4 py-2">Away</th>
                            <th class="px-4 py-2">Venue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#374151] text-[#F3F4F6]">
                        @forelse($games as $game)
                            <tr class="odd:bg-[#1f2937] even:bg-[#111827] hover:bg-[#374151] transition">
                                <td class="px-4 py-2">
                                    {{ $game->tipoff ? \Carbon\Carbon::parse($game->tipoff)->format('M d, H:i') : '-' }}
                                </td>
                                <td class="px-4 py-2">
                                    <a href="{{ route('nba.team.show', $game->home_team_id) }}"
                                       class="flex items-center space-x-2 hover:text-[#84CC16]">
                                        @if($game->home_team_logo)
                                            <img src="{{ $game->home_team_logo }}" class="h-6 w-6">
                                        @endif
                                        <span>{{ $game->home_team_name }}</span>
                                    </a>
                                </td>
                                <td class="px-4 py-2">
                                    <a href="{{ route('nba.team.show', $game->away_team_id) }}"
                                       class="flex items-center space-x-2 hover:text-[#84CC16]">
                                        @if($game->away_team_logo)
                                            <img src="{{ $game->away_team_logo }}" class="h-6 w-6">
                                        @endif
                                        <span>{{ $game->away_team_name }}</span>
                                    </a>
                                </td>
                                <td class="px-4 py-2">{{ $game->venue }} – {{ $game->city }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-2 text-gray-400">No upcoming games</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TEAM STATS -->
        <div>
            <h2 class="text-2xl font-semibold mb-4 text-white">Team Statistics</h2>
            <div class="bg-[#1f2937] rounded-lg p-6">
                @if($stats && $stats->games > 0)
                    <ul class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-gray-200">
                        <li><strong>Games:</strong> {{ $stats->games }}</li>
                        <li><strong>Wins:</strong> {{ $stats->wins }}</li>
                        <li><strong>Losses:</strong> {{ $stats->losses }}</li>
                        <li><strong>PPG:</strong> {{ number_format($stats->ppg, 1) }}</li>
                        <li><strong>RPG:</strong> {{ number_format($stats->rpg, 1) }}</li>
                        <li><strong>APG:</strong> {{ number_format($stats->apg, 1) }}</li>
                    </ul>
                @else
                    <p class="text-gray-400">Statistics not available.</p>
                @endif
            </div>
        </div>
    </main>
</body>

</html>
