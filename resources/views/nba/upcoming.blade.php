<!DOCTYPE html>
<html>
<head>
    <title>Upcoming NBA Games</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center p-6">

    <h1 class="text-3xl font-bold mb-6">Upcoming NBA Games</h1>

    @if(count($games) > 0)
        <div class="grid md:grid-cols-2 gap-6 w-full max-w-4xl">
            @foreach($games as $game)
                <div class="bg-white rounded-2xl shadow-md p-4 flex flex-col items-center">
                    <p class="text-lg font-semibold">{{ $game['home_team'] ?? 'Home' }} vs {{ $game['away_team'] ?? 'Away' }}</p>
                    <p class="mt-2 text-gray-700">
                        🗓 {{ \Carbon\Carbon::parse($game['game_date'])->format('M d, Y h:i A') }}
                    </p>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-gray-500">No upcoming games found.</p>
    @endif

</body>
</html>
