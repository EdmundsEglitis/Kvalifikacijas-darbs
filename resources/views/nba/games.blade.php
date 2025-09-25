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
        <div class="grid md:grid-cols-2 gap-6 w-full">
    @foreach($games as $game)
        <div class="bg-[#1f2937] rounded-xl shadow-lg p-6 flex flex-col items-center hover:bg-[#374151] transition">
            
            {{-- Teams row --}}
            <div class="flex items-center justify-between w-full">
                {{-- Home Team --}}
                <div class="flex items-center space-x-2 w-1/3 truncate">
                    @if($game->home_team_logo)
                        <img src="{{ $game->home_team_logo }}" alt="{{ $game->home_team_name }}" class="h-8 w-8 flex-shrink-0">
                    @endif
                    <span class="font-semibold text-[#84CC16] truncate">{{ $game->home_team_name ?? 'Home' }}</span>
                </div>

                {{-- VS --}}
                <div class="w-1/3 text-center text-gray-400 font-bold">vs</div>

                {{-- Away Team --}}
                <div class="flex items-center space-x-2 w-1/3 justify-end truncate">
                    <span class="font-semibold text-[#84CC16] truncate">{{ $game->away_team_name ?? 'Away' }}</span>
                    @if($game->away_team_logo)
                        <img src="{{ $game->away_team_logo }}" alt="{{ $game->away_team_name }}" class="h-8 w-8 flex-shrink-0">
                    @endif
                </div>
            </div>

            {{-- Date & Venue --}}
            <div class="mt-4 text-center">
                <p class="text-gray-300">
                    🗓 {{ $game->tipoff ? \Carbon\Carbon::parse($game->tipoff)->format('M d, Y h:i A') : '-' }}
                </p>
                <p class="text-sm text-gray-400 mt-1">
                    {{ $game->venue ?? 'Venue N/A' }} {{ $game->city ? '– '.$game->city : '' }}
                </p>
            </div>
        </div>
    @endforeach
</div>
        @endif
    </main>
</body>
</html>
