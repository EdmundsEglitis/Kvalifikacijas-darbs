<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>{{ $team['name'] }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    {{-- Navbar --}}
    <x-nba-navbar />

    {{-- Page content with top padding so it clears the fixed navbar --}}
    <main class="pt-24 px-6">
        <div class="max-w-3xl mx-auto bg-white shadow rounded p-6">
            <h1 class="text-3xl font-bold mb-4">{{ $team['name'] }}</h1>
            <img src="{{ $team['logo'] }}" alt="{{ $team['name'] }}" class="h-20 mb-4">
            <p><strong>Abbreviation:</strong> {{ $team['abbrev'] }}</p>
            <p class="mt-2">
                <a href="{{ $team['href'] }}" target="_blank" class="text-blue-600 hover:underline">
                    View on ESPN
                </a>
            </p>
            <p class="mt-4">
                <a href="{{ route('nba.players') }}" class="text-gray-600 hover:text-blue-600">← Back to Players</a>
            </p>
        </div>
    </main>
</body>
</html>
