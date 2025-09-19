<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>NBA – Upcoming Games</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <x-nba-navbar />

    <main class="pt-24 px-6 max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">NBA Games – Next 30 Days</h1>
        @php
    $grouped = collect($games)->groupBy('scheduleDate');
@endphp

@foreach($grouped as $date => $dayGames)
    <h2 class="text-xl font-semibold mt-6 mb-2">
        {{ \Carbon\Carbon::createFromFormat('Ymd', $date)->format('M d, Y') }}
    </h2>
    <div class="overflow-x-auto bg-white shadow rounded-lg mb-4">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-2">Time</th>
                    <th class="px-4 py-2">Home</th>
                    <th class="px-4 py-2">Away</th>
                    <th class="px-4 py-2">Venue</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
            @foreach($dayGames as $game)
    <tr class="hover:bg-gray-50">
        {{-- Time --}}
        <td class="px-4 py-2">
            {{ $game['tipoff'] ? \Carbon\Carbon::parse($game['tipoff'])->format('H:i') : '' }}
        </td>

        {{-- Home team --}}
        <td class="px-4 py-2  items-center space-x-2">
                <img src="{{ $game['homeTeam']['logo'] }}" class="h-6 w-6">
                <a href="{{ route('nba.team.show', $game['homeTeam']['id']) }}" class="text-blue-600 hover:underline">
                {{ $game['homeTeam']['name'] }}
                </a>
        </td>

        <td class="px-4 py-2 ">
        <img src="{{ $game['awayTeam']['logo'] }}" class="h-6 w-6"> 
        <a href="{{ route('nba.team.show', $game['awayTeam']['id']) }}" class="text-blue-600 hover:underline">
                    {{ $game['awayTeam']['name'] }}
                </a>

        </td>
        


        {{-- Venue --}}
        <td class="px-4 py-2">
            {{ $game['venue'] }} – {{ $game['city'] }}
        </td>
    </tr>
@endforeach

            </tbody>
        </table>
    </div>
@endforeach

    </main>
</body>
</html>
