
@extends('layouts.nba')
@section('title','Show game')

@section('content')

    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold mb-4">Game Summary</h1>
        <div class="bg-white p-6 rounded-lg shadow mb-6">
            <p><strong>Date:</strong> {{ $game->date->format('M d, Y H:i') }}</p>
            <p><strong>Teams:</strong> {{ $game->team1->name ?? 'TBD' }} vs {{ $game->team2->name ?? 'TBD' }}</p>
            <p><strong>Score:</strong> {{ $game->score ?? '-' }}</p>
            <p><strong>Winner:</strong> {{ $game->winner->name ?? '-' }}</p>
        </div>

        <h2 class="text-2xl font-bold mb-4">Player Stats</h2>
        <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-md">
            <thead>
                <tr class="bg-gray-100 text-left">
                    <th class="px-4 py-2 border-b">Player</th>
                    <th class="px-4 py-2 border-b">Team</th>
                    <th class="px-4 py-2 border-b">Minutes</th>
                    <th class="px-4 py-2 border-b">Points</th>
                    <th class="px-4 py-2 border-b">2PT</th>
                    <th class="px-4 py-2 border-b">3PT</th>
                    <th class="px-4 py-2 border-b">FT</th>
                    <th class="px-4 py-2 border-b">Rebounds</th>
                    <th class="px-4 py-2 border-b">Assists</th>
                    <th class="px-4 py-2 border-b">Steals</th>
                    <th class="px-4 py-2 border-b">Blocks</th>
                    <th class="px-4 py-2 border-b">Turnovers</th>
                    <th class="px-4 py-2 border-b">Fouls</th>
                    <th class="px-4 py-2 border-b">Efficiency</th>
                </tr>
            </thead>
            <tbody>
                @foreach($game->playerStats as $stat)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 border-b">{{ $stat->player->name ?? 'Unknown' }}</td>
                        <td class="px-4 py-2 border-b">{{ $stat->team->name ?? 'Unknown' }}</td>
                        <td class="px-4 py-2 border-b">{{ $stat->minutes ?? '-' }}</td>
                        <td class="px-4 py-2 border-b">{{ $stat->points }}</td>
                        <td class="px-4 py-2 border-b">{{ $stat->fgm2 }}/{{ $stat->fga2 }}</td>
                        <td class="px-4 py-2 border-b">{{ $stat->fgm3 }}/{{ $stat->fga3 }}</td>
                        <td class="px-4 py-2 border-b">{{ $stat->ftm }}/{{ $stat->fta }}</td>
                        <td class="px-4 py-2 border-b">{{ $stat->reb }}</td>
                        <td class="px-4 py-2 border-b">{{ $stat->ast }}</td>
                        <td class="px-4 py-2 border-b">{{ $stat->stl }}</td>
                        <td class="px-4 py-2 border-b">{{ $stat->blk }}</td>
                        <td class="px-4 py-2 border-b">{{ $stat->tov }}</td>
                        <td class="px-4 py-2 border-b">{{ $stat->pf }}</td>
                        <td class="px-4 py-2 border-b">{{ $stat->eff }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
