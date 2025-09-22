=<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>NBA – All Teams</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <x-nba-navbar />

    <main class="pt-24 px-6 max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">All NBA Teams</h1>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6">
            @foreach($teams as $team)
                <div class="bg-white shadow rounded-lg p-4 flex flex-col items-center hover:shadow-md transition">
                    @if(!empty($team['logo']))
                        <img src="{{ $team['logo'] }}" alt="{{ $team['name'] }}" class="h-16 w-16 mb-3">
                    @endif

                    <h2 class="text-sm font-semibold text-gray-800 text-center">
                        <a href="{{ route('nba.team.show', $team['id']) }}" class="text-blue-600 hover:underline">
                            {{ $team['name'] }}
                        </a>
                    </h2>

                    <p class="text-xs text-gray-500">{{ $team['short'] ?? '' }}</p>
                </div>
            @endforeach
        </div>
    </main>
</body>
</html>
