<!DOCTYPE html>
<html lang="lv">
<head>
  <meta charset="UTF-8">
  <title>{{ $player->name }} - Spēlētāja profils</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#111827] text-[#F3F4F6]">

  <x-team-navbar :parentLeagues="$parentLeagues" :team="$player->team" />

  <main class="pt-32 max-w-6xl mx-auto px-4 space-y-10">

    <!-- Player Info -->
    <section class="flex items-center space-x-6">
      @if($player->photo)
        <img src="{{ asset('storage/' . $player->photo) }}" class="h-32 w-32 rounded-full border-4 border-[#84CC16] object-cover">
      @endif
      <div>
        <h1 class="text-3xl font-bold">{{ $player->name }}</h1>
        <p class="text-[#9CA3AF]">Komanda: 
          <a href="{{ route('lbs.team.overview', $player->team->id) }}" class="text-[#84CC16] hover:underline">
            {{ $player->team->name }}
          </a>
        </p>
        <p class="text-[#9CA3AF]">Numurs: {{ $player->jersey_number ?? '—' }}</p>
        <p class="text-[#9CA3AF]">Augums: {{ $player->height ?? '—' }} cm</p>
        <p class="text-[#9CA3AF]">Dzimšanas diena: {{ $player->birthday ?? '—' }}</p>
        <p class="text-[#9CA3AF]">Tautība: {{ $player->nationality ?? '—' }}</p>
      </div>
    </section>

    <!-- Season Totals / Averages -->
    <section>
      <h2 class="text-2xl font-semibold mb-4">Sezonas kopsavilkums</h2>
      @if($totals['games'] > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
          <div class="bg-[#1f2937] p-4 rounded text-center">
            <div class="text-sm text-[#9CA3AF]">Spēles</div>
            <div class="text-xl font-bold">{{ $totals['games'] }}</div>
          </div>
          <div class="bg-[#1f2937] p-4 rounded text-center">
            <div class="text-sm text-[#9CA3AF]">Vid. punkti</div>
            <div class="text-xl font-bold">{{ $averages['points'] }}</div>
          </div>
          <div class="bg-[#1f2937] p-4 rounded text-center">
            <div class="text-sm text-[#9CA3AF]">Vid. atl. bumbas</div>
            <div class="text-xl font-bold">{{ $averages['reb'] }}</div>
          </div>
          <div class="bg-[#1f2937] p-4 rounded text-center">
            <div class="text-sm text-[#9CA3AF]">Vid. piespēles</div>
            <div class="text-xl font-bold">{{ $averages['ast'] }}</div>
          </div>
          <div class="bg-[#1f2937] p-4 rounded text-center">
            <div class="text-sm text-[#9CA3AF]">Vid. pārtvertās</div>
            <div class="text-xl font-bold">{{ $averages['stl'] }}</div>
          </div>
          <div class="bg-[#1f2937] p-4 rounded text-center">
            <div class="text-sm text-[#9CA3AF]">Vid. efektivitāte</div>
            <div class="text-xl font-bold">{{ $averages['eff'] }}</div>
          </div>
        </div>
      @else
        <p class="text-[#F3F4F6]/70">Nav pieejamas statistikas.</p>
      @endif
    </section>

    <!-- Per Game Stats -->
    <section>
      <h2 class="text-2xl font-semibold mb-4">Spēļu statistika</h2>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-[#374151]">
          <thead class="bg-[#1f2937]">
            <tr>
              <th class="px-2 py-2 text-sm">Datums</th>
              <th class="px-2 py-2 text-sm">Pretinieks</th>
              <th class="px-2 py-2 text-sm">Min</th>
              <th class="px-2 py-2 text-sm">Punkti</th>
              <th class="px-2 py-2 text-sm">2PM/2PA</th>
              <th class="px-2 py-2 text-sm">3PM/3PA</th>
              <th class="px-2 py-2 text-sm">FTM/FTA</th>
              <th class="px-2 py-2 text-sm">REB</th>
              <th class="px-2 py-2 text-sm">AST</th>
              <th class="px-2 py-2 text-sm">STL</th>
              <th class="px-2 py-2 text-sm">BLK</th>
              <th class="px-2 py-2 text-sm">TOV</th>
              <th class="px-2 py-2 text-sm">PF</th>
              <th class="px-2 py-2 text-sm">EFF</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#374151]">
  @foreach($player->playerGameStats as $stat)
    @php
      // Determine opponent team
      $opponent = $stat->game->team1->id === $player->team_id
        ? $stat->game->team2
        : $stat->game->team1;
    @endphp
    <tr>
      <td class="px-2 py-2 text-sm">{{ $stat->game->date->format('d.m.Y') }}</td>
      <td class="px-2 py-2 text-sm">
        <a href="{{ route('lbs.team.overview', $opponent->id) }}" 
           class="hover:text-[#84CC16]">
          {{ $opponent->name }}
        </a>
      </td>
      <td class="px-2 py-2 text-center">{{ $stat->minutes }}</td>
      <td class="px-2 py-2 text-center">{{ $stat->points }}</td>
      <td class="px-2 py-2 text-center">{{ $stat->fgm2 }}/{{ $stat->fga2 }}</td>
      <td class="px-2 py-2 text-center">{{ $stat->fgm3 }}/{{ $stat->fga3 }}</td>
      <td class="px-2 py-2 text-center">{{ $stat->ftm }}/{{ $stat->fta }}</td>
      <td class="px-2 py-2 text-center">{{ $stat->reb }}</td>
      <td class="px-2 py-2 text-center">{{ $stat->ast }}</td>
      <td class="px-2 py-2 text-center">{{ $stat->stl }}</td>
      <td class="px-2 py-2 text-center">{{ $stat->blk }}</td>
      <td class="px-2 py-2 text-center">{{ $stat->tov }}</td>
      <td class="px-2 py-2 text-center">{{ $stat->pf }}</td>
      <td class="px-2 py-2 text-center">{{ $stat->eff }}</td>
    </tr>
  @endforeach
</tbody>

        </table>
      </div>
    </section>

  </main>
</body>
</html>
