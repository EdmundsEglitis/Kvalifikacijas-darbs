<!DOCTYPE html>
<html lang="lv" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <title>{{ $player->name }} – Spēlētāja profils</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#111827] text-[#F3F4F6]">

  <x-team-navbar :parentLeagues="$parentLeagues" :team="$player->team" />

  <main class="pt-32 max-w-6xl mx-auto px-4 space-y-16">

    <!-- Player Info -->
    <section class="flex flex-col md:flex-row items-center md:items-start md:space-x-8 space-y-6 md:space-y-0">
      @if($player->photo)
        <img src="{{ asset('storage/' . $player->photo) }}"
             alt="{{ $player->name }}"
             class="h-36 w-36 rounded-full border-4 border-[#84CC16]/60 shadow-lg object-cover">
      @else
        <div class="h-36 w-36 rounded-full bg-gray-700 flex items-center justify-center text-gray-400 text-sm">
          No Photo
        </div>
      @endif

      <div class="space-y-2 text-center md:text-left">
        <h1 class="text-4xl font-extrabold text-white drop-shadow">{{ $player->name }}</h1>
        <p class="text-[#9CA3AF]">Komanda:
          <a href="{{ route('lbs.team.overview', $player->team->id) }}"
             class="text-[#84CC16] hover:underline font-medium">
            {{ $player->team->name }}
          </a>
        </p>
        <div class="flex flex-wrap gap-4 text-sm text-[#9CA3AF]">
          <span>Numurs: <span class="font-semibold text-white">{{ $player->jersey_number ?? '—' }}</span></span>
          <span>Augums: <span class="font-semibold text-white">{{ $player->height ?? '—' }} cm</span></span>
          <span>Dzimšanas diena: <span class="font-semibold text-white">{{ $player->birthday ?? '—' }}</span></span>
          <span>Tautība: <span class="font-semibold text-white">{{ $player->nationality ?? '—' }}</span></span>
        </div>
      </div>
    </section>

    <!-- Season Totals / Averages -->
    <section>
      <h2 class="text-2xl font-bold text-white mb-6">Sezonas kopsavilkums</h2>
      @if($totals['games'] > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-6">
          @foreach([
            ['label' => 'Spēles', 'value' => $totals['games']],
            ['label' => 'Vid. punkti', 'value' => $averages['points']],
            ['label' => 'Vid. atl. bumbas', 'value' => $averages['reb']],
            ['label' => 'Vid. piespēles', 'value' => $averages['ast']],
            ['label' => 'Vid. pārtvertās', 'value' => $averages['stl']],
            ['label' => 'Vid. efektivitāte', 'value' => $averages['eff']]
          ] as $stat)
            <div class="bg-[#1f2937] p-5 rounded-lg text-center shadow border border-[#374151] hover:border-[#84CC16] transition">
              <div class="text-sm text-[#9CA3AF]">{{ $stat['label'] }}</div>
              <div class="text-2xl font-bold text-[#84CC16]">{{ $stat['value'] }}</div>
            </div>
          @endforeach
        </div>
      @else
        <p class="text-[#F3F4F6]/70">Nav pieejamas statistikas.</p>
      @endif
    </section>

    <!-- Per Game Stats -->
    <section>
      <h2 class="text-2xl font-bold text-white mb-6">Spēļu statistika</h2>
      <div class="overflow-x-auto rounded-xl shadow-lg border border-[#374151]">
        <table class="min-w-full divide-y divide-[#374151]">
          <thead class="bg-[#0f172a] sticky top-0 z-10">
            <tr>
              @foreach(['Datums','Pretinieks','Min','Punkti','2PM/2PA','3PM/3PA','FTM/FTA','REB','AST','STL','BLK','TOV','PF','EFF'] as $head)
                <th class="px-3 py-3 text-xs font-semibold text-[#F3F4F6]/70 uppercase text-center">{{ $head }}</th>
              @endforeach
            </tr>
          </thead>
          <tbody class="divide-y divide-[#374151]">
            @foreach($player->playerGameStats as $stat)
              @php
                $opponent = $stat->game->team1->id === $player->team_id
                  ? $stat->game->team2
                  : $stat->game->team1;
              @endphp
              <tr class="hover:bg-[#2d3748] transition">
                <td class="px-3 py-2 text-sm text-center">{{ $stat->game->date->format('d.m.Y') }}</td>
                <td class="px-3 py-2 text-sm text-center">
                  <a href="{{ route('lbs.team.overview', $opponent->id) }}" class="hover:text-[#84CC16] font-medium">
                    {{ $opponent->name }}
                  </a>
                </td>
                <td class="px-3 py-2 text-center">{{ $stat->minutes }}</td>
                <td class="px-3 py-2 text-center font-semibold text-[#84CC16]">{{ $stat->points }}</td>
                <td class="px-3 py-2 text-center">{{ $stat->fgm2 }}/{{ $stat->fga2 }}</td>
                <td class="px-3 py-2 text-center">{{ $stat->fgm3 }}/{{ $stat->fga3 }}</td>
                <td class="px-3 py-2 text-center">{{ $stat->ftm }}/{{ $stat->fta }}</td>
                <td class="px-3 py-2 text-center">{{ $stat->reb }}</td>
                <td class="px-3 py-2 text-center">{{ $stat->ast }}</td>
                <td class="px-3 py-2 text-center">{{ $stat->stl }}</td>
                <td class="px-3 py-2 text-center">{{ $stat->blk }}</td>
                <td class="px-3 py-2 text-center">{{ $stat->tov }}</td>
                <td class="px-3 py-2 text-center">{{ $stat->pf }}</td>
                <td class="px-3 py-2 text-center font-semibold">{{ $stat->eff }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </section>

  </main>
</body>
</html>
