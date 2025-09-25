<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Upcoming NBA Games</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="antialiased text-[#F3F4F6] bg-[#111827] min-h-screen p-6">
    <x-nba-navbar />

    <main class="pt-20 max-w-5xl mx-auto">
        <h1 class="text-3xl font-bold text-white mb-8">Upcoming NBA Games</h1>

        @if($games->count() > 0)
            <div class="grid md:grid-cols-2 gap-6">
                @foreach($games as $game)
                    <div class="bg-[#1f2937] rounded-xl shadow-lg p-6 flex flex-col items-center hover:bg-[#374151] transition">
                        <p class="flex items-center space-x-3 text-lg font-semibold text-[#84CC16]">
                            {{-- Home team --}}
                            @if($game->homeTeam && $game->homeTeam->logo)
                                <img src="{{ $game->homeTeam->logo }}" alt="{{ $game->homeTeam->name }}" class="h-8 w-8">
                            @endif
                            <span>{{ $game->homeTeam->name ?? 'Home' }}</span>

                            <span class="text-gray-400">vs</span>

                            {{-- Away team --}}
                            <span>{{ $game->awayTeam->name ?? 'Away' }}</span>
                            @if($game->awayTeam && $game->awayTeam->logo)
                                <img src="{{ $game->awayTeam->logo }}" alt="{{ $game->awayTeam->name }}" class="h-8 w-8">
                            @endif
                        </p>

                        <p class="mt-3 text-gray-300">
                            🗓 {{ $game->tipoff ? \Carbon\Carbon::parse($game->tipoff)->format('M d, Y h:i A') : '-' }}
                        </p>

                        <p class="mt-1 text-sm text-gray-400">
                            {{ $game->venue ?? 'Venue N/A' }} {{ $game->city ? '– '.$game->city : '' }}
                        </p>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-400">No upcoming games found.</p>
        @endif
    </main>
</body>
</html>
