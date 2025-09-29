<!DOCTYPE html>
<html lang="lv" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <title>{{ $player->name }} – Spēlētāja profils</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @keyframes fadeUp { from {opacity:0; transform:translateY(8px)} to {opacity:1; transform:translateY(0)} }
    .fade-up { animation: fadeUp .45s ease both }
  </style>
</head>
<body class="bg-[#111827] text-[#F3F4F6] antialiased">
  <x-team-navbar :parentLeagues="$parentLeagues" :team="$player->team" />

  <main class="pt-28 sm:pt-32 max-w-6xl mx-auto px-4 space-y-12 sm:space-y-16">

    <!-- Player Info -->
    <section class="fade-up grid grid-cols-1 md:grid-cols-[auto,1fr] items-start gap-6 md:gap-8">
      <div class="flex md:block justify-center">
        @if($player->photo)
          <img src="{{ asset('storage/' . $player->photo) }}"
               alt="{{ $player->name }}"
               class="h-28 w-28 sm:h-36 sm:w-36 rounded-full border-4 border-[#84CC16]/60 shadow-lg object-cover">
        @else
          <div class="h-28 w-28 sm:h-36 sm:w-36 rounded-full bg-gray-700 flex items-center justify-center text-gray-400 text-xs sm:text-sm">
            No Photo
          </div>
        @endif
      </div>

      <div class="space-y-4 text-center md:text-left">
        <div>
          <h1 class="text-2xl sm:text-4xl font-extrabold text-white leading-tight">{{ $player->name }}</h1>
          <p class="mt-1 text-[#9CA3AF]">
            Komanda:
            <a href="{{ route('lbs.team.overview', $player->team->id) }}"
               class="text-[#84CC16] hover:underline font-medium">
              {{ $player->team->name }}
            </a>
          </p>
        </div>

        <div class="flex flex-wrap items-center justify-center md:justify-start gap-2 sm:gap-3 text-sm text-[#9CA3AF]">
          <span class="px-2.5 py-1 rounded-full bg-white/5 border border-white/10">
            Numurs: <span class="font-semibold text-white">{{ $player->jersey_number ?? '—' }}</span>
          </span>
          <span class="px-2.5 py-1 rounded-full bg-white/5 border border-white/10">
            Augums: <span class="font-semibold text-white">{{ $player->height ?? '—' }} cm</span>
          </span>
          <span class="px-2.5 py-1 rounded-full bg-white/5 border border-white/10">
            Dzimšanas diena: <span class="font-semibold text-white">{{ $player->birthday ?? '—' }}</span>
          </span>
          <span class="px-2.5 py-1 rounded-full bg-white/5 border border-white/10">
            Tautība: <span class="font-semibold text-white">{{ $player->nationality ?? '—' }}</span>
          </span>
        </div>
      </div>
    </section>

    <!-- Season Totals / Averages -->
    <section class="fade-up">
      <h2 class="text-xl sm:text-2xl font-bold text-white mb-4 sm:mb-6">Sezonas kopsavilkums</h2>
      @if($totals['games'] > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
          @foreach([
            ['label' => 'Spēles', 'value' => $totals['games']],
            ['label' => 'Vid. punkti', 'value' => $averages['points']],
            ['label' => 'Vid. atl. bumbas', 'value' => $averages['reb']],
            ['label' => 'Vid. piespēles', 'value' => $averages['ast']],
            ['label' => 'Vid. pārtvertās', 'value' => $averages['stl']],
            ['label' => 'Vid. efektivitāte', 'value' => $averages['eff']]
          ] as $stat)
            <div class="bg-[#1f2937] p-4 sm:p-5 rounded-xl text-center shadow border border-[#374151] hover:border-[#84CC16] transition">
              <div class="text-xs sm:text-sm text-[#9CA3AF]">{{ $stat['label'] }}</div>
              <div class="mt-0.5 text-xl sm:text-2xl font-extrabold text-[#84CC16]">{{ $stat['value'] }}</div>
            </div>
          @endforeach
        </div>
      @else
        <p class="text-[#F3F4F6]/70">Nav pieejamas statistikas.</p>
      @endif
    </section>

    <!-- Per Game Stats -->
    <section class="fade-up">
      <h2 class="text-xl sm:text-2xl font-bold text-white mb-4 sm:mb-6">Spēļu statistika</h2>

      <!-- Mobile CARDS -->
      <div class="space-y-3 sm:space-y-4 md:hidden">
        @foreach($player->playerGameStats as $stat)
          @php
            $opponent = $stat->game->team1->id === $player->team_id ? $stat->game->team2 : $stat->game->team1;
          @endphp
          <article class="bg-[#1f2937] border border-[#374151] rounded-xl p-4 shadow hover:shadow-lg transition">
            <div class="flex items-start justify-between gap-3">
              <div>
                <div class="text-sm text-gray-300">{{ $stat->game->date->format('d.m.Y') }}</div>
                <a href="{{ route('lbs.team.overview', $opponent->id) }}"
                   class="block text-base font-semibold text-white hover:text-[#84CC16] truncate">
                  {{ $opponent->name }}
                </a>
              </div>
              <div class="text-right">
                <div class="text-xs text-gray-400">Punkti</div>
                <div class="text-lg font-bold text-[#84CC16]">{{ $stat->points }}</div>
              </div>
            </div>

            <dl class="mt-3 grid grid-cols-3 gap-2 text-xs">
              <div class="rounded-lg bg-white/5 border border-white/10 p-2 text-center">
                <dt class="text-gray-400">Min</dt><dd class="font-semibold">{{ $stat->minutes }}</dd>
              </div>
              <div class="rounded-lg bg-white/5 border border-white/10 p-2 text-center">
                <dt class="text-gray-400">2PM/2PA</dt><dd class="font-semibold">{{ $stat->fgm2 }}/{{ $stat->fga2 }}</dd>
              </div>
              <div class="rounded-lg bg-white/5 border border-white/10 p-2 text-center">
                <dt class="text-gray-400">3PM/3PA</dt><dd class="font-semibold">{{ $stat->fgm3 }}/{{ $stat->fga3 }}</dd>
              </div>
              <div class="rounded-lg bg-white/5 border border-white/10 p-2 text-center">
                <dt class="text-gray-400">FTM/FTA</dt><dd class="font-semibold">{{ $stat->ftm }}/{{ $stat->fta }}</dd>
              </div>
              <div class="rounded-lg bg-white/5 border border-white/10 p-2 text-center">
                <dt class="text-gray-400">REB</dt><dd class="font-semibold">{{ $stat->reb }}</dd>
              </div>
              <div class="rounded-lg bg-white/5 border border-white/10 p-2 text-center">
                <dt class="text-gray-400">AST</dt><dd class="font-semibold">{{ $stat->ast }}</dd>
              </div>
              <div class="rounded-lg bg-white/5 border border-white/10 p-2 text-center">
                <dt class="text-gray-400">STL</dt><dd class="font-semibold">{{ $stat->stl }}</dd>
              </div>
              <div class="rounded-lg bg-white/5 border border-white/10 p-2 text-center">
                <dt class="text-gray-400">BLK</dt><dd class="font-semibold">{{ $stat->blk }}</dd>
              </div>
              <div class="rounded-lg bg-white/5 border border-white/10 p-2 text-center">
                <dt class="text-gray-400">TOV</dt><dd class="font-semibold">{{ $stat->tov }}</dd>
              </div>
              <div class="rounded-lg bg-white/5 border border-white/10 p-2 text-center">
                <dt class="text-gray-400">PF</dt><dd class="font-semibold">{{ $stat->pf }}</dd>
              </div>
              <div class="rounded-lg bg-white/5 border border-white/10 p-2 text-center col-span-3">
                <dt class="text-gray-400">EFF</dt><dd class="font-semibold">{{ $stat->eff }}</dd>
              </div>
            </dl>
          </article>
        @endforeach
      </div>

      <!-- Desktop TABLE -->
      <div class="hidden md:block overflow-x-auto rounded-xl shadow-lg border border-[#374151]">
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
              <tr class="hover:bg-[#223041] transition">
                <td class="px-3 py-2 text-sm text-center whitespace-nowrap">{{ $stat->game->date->format('d.m.Y') }}</td>
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
