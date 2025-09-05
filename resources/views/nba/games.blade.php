<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All NBA Games</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

    <!-- Navbar -->
    <nav class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('home') }}" class="text-xl font-bold text-gray-800">NBA Dashboard</a>
                    <a href="{{ route('games') }}" class="text-gray-600 hover:text-gray-900">All Games</a>
                    <a href="{{ route('players') }}" class="text-gray-600 hover:text-gray-900">All Players</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold mb-6">All NBA Games</h1>

        <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-md">
            <thead>
                <tr class="bg-gray-100 text-left">
                    <th class="px-4 py-2 border-b">Date</th>
                    <th class="px-4 py-2 border-b">Team 1</th>
                    <th class="px-4 py-2 border-b">Team 2</th>
                    <th class="px-4 py-2 border-b">Score</th>
                    <th class="px-4 py-2 border-b">Winner</th>
                </tr>
            </thead>
            <tbody>
                @foreach($games as $game)
                    <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('games.show', $game->id) }}'">
                        <td class="px-4 py-2 border-b">{{ $game->date->format('M d, Y H:i') }}</td>
                        <td class="px-4 py-2 border-b">{{ $game->team1->name ?? 'TBD' }}</td>
                        <td class="px-4 py-2 border-b">{{ $game->team2->name ?? 'TBD' }}</td>
                        <td class="px-4 py-2 border-b">{{ $game->score ?? '-' }}</td>
                        <td class="px-4 py-2 border-b">{{ $game->winner->name ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
