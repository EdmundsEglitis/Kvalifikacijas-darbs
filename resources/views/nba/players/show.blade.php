<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $player->full_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="antialiased text-[#F3F4F6] bg-[#111827]">
<x-nba-navbar />

    <main class="pt-24 max-w-7xl mx-auto px-4 space-y-10">
        <!-- HEADER -->
        <div class="bg-[#1f2937] rounded-xl p-6 flex items-center space-x-6">
            <img src="{{ $player->image ?? $details->headshot_href ?? 'https://via.placeholder.com/120' }}"
                 class="h-28 w-28 rounded-full ring-4 ring-[#84CC16] object-cover">
            <div>
                <h1 class="text-3xl font-bold">{{ $player->full_name }}</h1>
                <p class="text-gray-300">
                    {{ $details->position['displayName'] ?? '' }}
                    @if($teamHeader)
                        <a href="{{ route('nba.team.show', $teamHeader->external_id) }}"
                             class="text-[#84CC16] hover:underline inline-flex items-center space-x-2">
                            <img src="{{ $teamHeader->logo }}" class="h-5 w-5">
                            <span>{{ $teamHeader->name }}</span>
                          </a>
                    @endif
                </p>
                <p class="text-gray-400">Jersey: {{ $details->display_jersey ?? '-' }}</p>
            </div>
        </div>

        <!-- BIO -->
        <div class="bg-[#1f2937] rounded-xl p-6">
            <h2 class="text-2xl font-semibold mb-4">Bio</h2>
            <ul class="grid grid-cols-2 gap-4 text-sm">
                <li><strong>Height:</strong> {{ $details->display_height ?? '-' }}</li>
                <li><strong>Weight:</strong> {{ $details->display_weight ?? '-' }}</li>
                <li><strong>Age:</strong> {{ $details->age ?? '-' }}</li>
                <li><strong>DOB:</strong> {{ $details->display_dob ?? '-' }}</li>
                <li><strong>Birthplace:</strong> {{ $details->birth_place ?? '-' }}</li>
                <li><strong>Experience:</strong> {{ $details->display_experience ?? '-' }}</li>
                <li><strong>Draft:</strong> {{ $details->display_draft ?? '-' }}</li>
                <li><strong>Status:</strong> {{ $cleanStatus ?? '-' }}</li>
            </ul>
        </div>

        <!-- CAREER AVERAGES -->
        <div class="bg-[#1f2937] rounded-xl p-6">
            <h2 class="text-2xl font-semibold mb-4">Career Averages</h2>
            @if($career && $career->games > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 text-center">
                    <div><p class="font-bold text-[#84CC16]">{{ number_format($career->pts,1) }}</p><p>PPG</p></div>
                    <div><p class="font-bold text-[#84CC16]">{{ number_format($career->reb,1) }}</p><p>RPG</p></div>
                    <div><p class="font-bold text-[#84CC16]">{{ number_format($career->ast,1) }}</p><p>APG</p></div>
                    <div><p class="font-bold text-[#84CC16]">{{ number_format($career->stl,1) }}</p><p>SPG</p></div>
                    <div><p class="font-bold text-[#84CC16]">{{ number_format($career->blk,1) }}</p><p>BPG</p></div>
                    <div><p class="font-bold text-[#84CC16]">{{ number_format($career->min,1) }}</p><p>MIN</p></div>
                </div>
            @else
                <p class="text-gray-400">No stats available.</p>
            @endif
        </div>

        <!-- SEASON AVERAGES -->
        <div class="bg-[#1f2937] rounded-xl p-6">
            <h2 class="text-2xl font-semibold mb-4">{{ now()->year }} Season Averages</h2>
            @if($season && $season->games > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 text-center">
                    <div><p class="font-bold text-[#84CC16]">{{ number_format($season->pts,1) }}</p><p>PPG</p></div>
                    <div><p class="font-bold text-[#84CC16]">{{ number_format($season->reb,1) }}</p><p>RPG</p></div>
                    <div><p class="font-bold text-[#84CC16]">{{ number_format($season->ast,1) }}</p><p>APG</p></div>
                    <div><p class="font-bold text-[#84CC16]">{{ number_format($season->stl,1) }}</p><p>SPG</p></div>
                    <div><p class="font-bold text-[#84CC16]">{{ number_format($season->blk,1) }}</p><p>BPG</p></div>
                    <div><p class="font-bold text-[#84CC16]">{{ number_format($season->min,1) }}</p><p>MIN</p></div>
                </div>
            @else
                <p class="text-gray-400">No stats available for {{ now()->year }}.</p>
            @endif
        </div>

        <!-- GAME LOGS -->
        <div class="bg-[#1f2937] rounded-xl p-6">
            <h2 class="text-2xl font-semibold mb-4">Game Logs</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-[#111827] border-b border-[#374151] text-gray-400">
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
                    <tbody class="divide-y divide-[#374151]">
                        @forelse($gamelogs as $log)
                            <tr class="odd:bg-[#1f2937] even:bg-[#111827] hover:bg-[#374151]">
                                <td class="px-4 py-2">
                                    {{ $log->game_date ? \Carbon\Carbon::parse($log->game_date)->format('M d, Y') : '-' }}
                                </td>
                                <td class="px-4 py-2 flex items-center space-x-2">
                                    @if($log->opponent_team_id ?? false)
                                        <a href="{{ route('nba.team.show', $log->opponent_team_id) }}"
                                           class="flex items-center space-x-2 hover:text-[#84CC16]">
                                            <img src="{{ $log->opponent_team_logo }}" class="h-6 w-6 rounded-full">
                                            <span>{{ $log->opponent_team_name }}</span>
                                        </a>
                                    @else
                                        {{ $log->opponent_name ?? '-' }}
                                    @endif
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
                                <td class="px-4 py-2 font-bold text-[#84CC16]">{{ $log->points ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="18" class="text-center py-4 text-gray-400">No gamelogs available.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
