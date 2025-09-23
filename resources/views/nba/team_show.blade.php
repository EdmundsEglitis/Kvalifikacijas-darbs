<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $team['name'] }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <x-nba-navbar />

    <main class="pt-24 px-6 max-w-7xl mx-auto">
        <div class="bg-white shadow rounded p-6 mb-8">
            <div class="flex items-center space-x-4">
                <img src="{{ $team['logo'] }}" alt="{{ $team['name'] }}" class="h-20">
                <div>
                    <h1 class="text-3xl font-bold">{{ $team['name'] }}</h1>
                    <p class="text-gray-600">Abbreviation: {{ $team['abbrev'] }}</p>
                    <a href="{{ $team['href'] }}" target="_blank" class="text-blue-600 hover:underline">
                        View on ESPN
                    </a>
                </div>
            </div>
        </div>

<h2 class="text-2xl font-semibold mb-4">Roster</h2>
<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6 mb-8">
    @foreach($players as $player)
        <a href="{{ route('nba.player.show', $player['id']) }}" 
           class="bg-white shadow rounded-lg p-4 flex flex-col items-center hover:shadow-md transition">
           
            @if(!empty($player['image']))
                <img src="{{ $player['image'] }}" 
                     alt="{{ $player['fullName'] }}" 
                     class="h-16 w-16 rounded-full mb-2">
            @else
                <div class="h-16 w-16 rounded-full mb-2 flex items-center justify-center bg-gray-200 text-gray-500 text-xs">
                    No Photo
                </div>
            @endif

            <h3 class="text-sm font-semibold text-gray-800 text-center">
                {{ $player['fullName'] }}
            </h3>
            <p class="text-xs text-gray-500">{{ $player['position'] ?? '' }}</p>
        </a>
    @endforeach
</div>

<h2 class="text-2xl font-semibold mb-4">Upcoming Games</h2>
<div class="overflow-x-auto bg-white shadow rounded-lg mb-8">
    <table class="min-w-full text-left text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-2">Date</th>
                <th class="px-4 py-2">Home</th>
                <th class="px-4 py-2">Away</th>
                <th class="px-4 py-2">Venue</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($games as $game)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2">
                        {{ \Carbon\Carbon::parse($game['tipoff'])->format('M d, H:i') }}
                    </td>

                    {{-- Home team clickable --}}
                    <td class="px-4 py-2 ">
                        <a href="{{ route('nba.team.show', $game['homeTeam']['id']) }}" class="flex items-center space-x-2 text-blue-600 hover:underline">
                            <img src="{{ $game['homeTeam']['logo'] }}" class="h-6 w-6">
                            <span>{{ $game['homeTeam']['name'] }}</span>
                        </a>
                    </td>

                    {{-- Away team clickable --}}
                    <td class="px-4 py-2">
                        <a href="{{ route('nba.team.show', $game['awayTeam']['id']) }}" class="flex items-center space-x-2 text-blue-600 hover:underline">
                            <img src="{{ $game['awayTeam']['logo'] }}" class="h-6 w-6">
                            <span>{{ $game['awayTeam']['name'] }}</span>
                        </a>
                    </td>

                    <td class="px-4 py-2">
                        {{ $game['venue'] }} – {{ $game['city'] }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-2 text-gray-500">No upcoming games</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

    <h2 class="text-2xl font-semibold mb-4">Team Statistics</h2>
        <div class="bg-white shadow rounded-lg p-6">
            @if(!empty($stats))
                <ul class="grid grid-cols-2 gap-4">
                    <li><strong>Wins:</strong> {{ $stats['wins'] }}</li>
                    <li><strong>Losses:</strong> {{ $stats['losses'] }}</li>
                    <li><strong>PPG:</strong> {{ $stats['pointsPerGame'] }}</li>
                    <li><strong>RPG:</strong> {{ $stats['reboundsPerGame'] }}</li>
                    <li><strong>APG:</strong> {{ $stats['assistsPerGame'] }}</li>
                </ul>
            @else
                <p class="text-gray-500">Statistics not available.</p>
            @endif
        </div>
    </main>
</body>
</html>
