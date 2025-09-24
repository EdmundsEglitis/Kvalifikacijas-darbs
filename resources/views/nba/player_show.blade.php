<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $player->full_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <x-nba-navbar />

    <main class="pt-24 px-6 max-w-6xl mx-auto">
        <!-- Player header -->
        <div class="bg-white shadow rounded p-6 mb-8 flex items-center space-x-6">
            <img src="{{ $player->headshot_href ?? 'https://via.placeholder.com/120' }}"
                 alt="{{ $player->full_name }}"
                 class="h-28 w-28 rounded-full object-cover">
            <div>
                <h1 class="text-3xl font-bold">{{ $player->full_name }}</h1>
                <p class="text-gray-600">
                    {{ $player->position ?? '' }}
                    @if($player->team)
                        | <a href="{{ route('nba.team.show', $player->team_id ?? '#') }}"
                             class="text-blue-600 hover:underline">
                            {{ $player->team_name ?? '' }}
                          </a>
                    @endif
                </p>
                <p class="text-gray-500">Jersey: {{ $player->display_jersey ?? '-' }}</p>
            </div>
        </div>

        <!-- Bio -->
        <div class="bg-white shadow rounded p-6 mb-8">
            <h2 class="text-2xl font-semibold mb-4">Bio</h2>
            <ul class="grid grid-cols-2 gap-4 text-sm">
                <li><strong>Height:</strong> {{ $player->display_height ?? '-' }}</li>
                <li><strong>Weight:</strong> {{ $player->display_weight ?? '-' }}</li>
                <li><strong>Age:</strong> {{ $player->age ?? '-' }}</li>
                <li><strong>DOB:</strong> {{ $player->display_dob ?? '-' }}</li>
                <li><strong>Birthplace:</strong> {{ $player->birth_place ?? '-' }}</li>
                <li><strong>Experience:</strong> {{ $player->display_experience ?? '-' }}</li>
                <li><strong>Draft:</strong> {{ $player->display_draft ?? '-' }}</li>
                <li><strong>Status:</strong> {{ $player->status ?? '-' }}</li>
            </ul>
        </div>

        <!-- Gamelogs -->
        <div class="bg-white shadow rounded p-6 mb-8">
            <h2 class="text-2xl font-semibold mb-4">Game Logs</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-gray-50 border-b sticky top-0">
                        <tr>
                            <th class="px-4 py-2">Date</th>
                            <th class="px-4 py-2">Opponent</th>
                            <th class="px-4 py-2">Result</th>
                            <th class="px-4 py-2">Score</th>
                            <th class="px-4 py-2">MIN</th>
                            <th class="px-4 py-2">FG</th>
                            <th class="px-4 py-2">FG%</th>
                            <th class="px-4 py-2">3PT</th>
                            <th class="px-4 py-2">3PT%</th>
                            <th class="px-4 py-2">FT</th>
                            <th class="px-4 py-2">FT%</th>
                            <th class="px-4 py-2">REB</th>
                            <th class="px-4 py-2">AST</th>
                            <th class="px-4 py-2">STL</th>
                            <th class="px-4 py-2">BLK</th>
                            <th class="px-4 py-2">TO</th>
                            <th class="px-4 py-2">PF</th>
                            <th class="px-4 py-2">PTS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($gamelog as $log)
                            <tr class="odd:bg-white even:bg-gray-50 hover:bg-gray-100">
                                <td class="px-4 py-2">{{ $log->game_date ? \Carbon\Carbon::parse($log->game_date)->format('M d, Y') : '-' }}</td>
                                <td class="px-4 py-2 flex items-center space-x-2">
                                    @if($log->opponent_logo)
                                        <img src="{{ $log->opponent_logo }}" class="h-6 w-6 rounded-full">
                                    @endif
                                    {{ $log->opponent_name ?? '-' }}
                                </td>
                                <td class="px-4 py-2">{{ $log->result ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $log->score ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $log->minutes ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $log->fg ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $log->fg_pct ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $log->three_pt ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $log->three_pt_pct ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $log->ft ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $log->ft_pct ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $log->rebounds ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $log->assists ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $log->steals ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $log->blocks ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $log->turnovers ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $log->fouls ?? '-' }}</td>
                                <td class="px-4 py-2 font-bold">{{ $log->points ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="18" class="px-4 py-4 text-center text-gray-500">
                                    No gamelogs available.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
