<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>{{ $player['firstName'] }} {{ $player['lastName'] }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    {{-- Navbar --}}
    <x-nba-navbar />

    {{-- Page content with top padding so it clears the fixed navbar --}}
    <main class="pt-24 px-6">
        <div class="max-w-3xl mx-auto bg-white shadow rounded p-6">
            <h1 class="text-3xl font-bold mb-4">{{ $player['firstName'] }} {{ $player['lastName'] }}</h1>

            @if(!empty($player['image']))
                <img src="{{ $player['image'] }}" alt="Photo" class="h-32 w-32 rounded-full mb-4">
            @endif

            <p><strong>Team:</strong>
                <a href="{{ route('nba.team.show', $player['teamId']) }}" class="text-blue-600 hover:underline">
                    {{ $player['teamName'] }}
                </a>
            </p>
            <p><strong>Position:</strong> {{ $player['position'] ?? '-' }}</p>
            <p><strong>Height:</strong> {{ $player['displayHeight'] ?? '-' }}</p>
            <p><strong>Weight:</strong> {{ $player['displayWeight'] ?? '-' }}</p>

            <p class="mt-4">
                <a href="{{ route('nba.players') }}" class="text-gray-600 hover:text-blue-600">← Back to Players</a>
            </p>
        </div>
    </main>
</body>
</html>
