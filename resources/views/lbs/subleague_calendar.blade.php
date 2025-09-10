<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subLeague->name }} - Kalendārs</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

    <!-- Navbar (main + sub-league tabs) -->
    @include('lbs.partials.navbar', ['parentLeagues' => $parentLeagues, 'subLeague' => $subLeague])

    <main class="pt-32 max-w-7xl mx-auto px-4">
        <h1 class="text-3xl font-bold text-gray-800">{{ $subLeague->name }} - Kalendārs</h1>

        @if($games->isEmpty())
            <p class="mt-4 text-gray-500">Nav pieejamu spēļu.</p>
        @else
            <table class="min-w-full mt-4 border border-gray-300 table-auto">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="px-2 py-1 border">Datums</th>
                        <th class="px-2 py-1 border">Komanda 1</th>
                        <th class="px-2 py-1 border">Komanda 2</th>
                        <th class="px-2 py-1 border">Rezultāts</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($games as $game)
                        <tr>
                            <td class="px-2 py-1 border">{{ $game->date }}</td>
                            <td class="px-2 py-1 border">{{ $game->team1->name }}</td>
                            <td class="px-2 py-1 border">{{ $game->team2->name }}</td>
                            <td class="px-2 py-1 border">{{ $game->score ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </main>
</body>
</html>
