<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subLeague->name }} - Statistika</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

    <!-- Navbar (main + sub-league tabs) -->
    @include('lbs.partials.navbar', ['parentLeagues' => $parentLeagues, 'subLeague' => $subLeague])

    <main class="pt-32 max-w-7xl mx-auto px-4">
        <h1 class="text-3xl font-bold text-gray-800">{{ $subLeague->name }} - Statistika</h1>

        @if($teamsStats->isEmpty())
            <p class="mt-4 text-gray-500">Nav pieejamu komandu datu.</p>
        @else
            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                @foreach($teamsStats as $teamStat)
                    <div class="p-4 bg-white shadow rounded-lg text-center">
                        <h2 class="text-xl font-semibold">{{ $teamStat['team']->name }}</h2>
                        <p class="mt-2 font-bold">Uzvaras: {{ $teamStat['wins'] }}</p>
                        <p class="mt-1 font-bold">Zaudējumi: {{ $teamStat['losses'] }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </main>
</body>
</html>
