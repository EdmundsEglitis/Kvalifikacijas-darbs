<!DOCTYPE html>
<html lang="lv" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $team->name }} - Komandas statistika</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="antialiased bg-[#111827] text-[#F3F4F6]">

<x-team-navbar :parentLeagues="$parentLeagues" :team="$team" />


  <main class="pt-32 max-w-7xl mx-auto px-4 space-y-12">

    <!-- Team Logo + Name -->
    <div class="flex flex-col items-center space-y-4">
      @if($team->logo)
        <img src="{{ asset('storage/' . $team->logo) }}"
             alt="{{ $team->name }}"
             class="h-24 w-24 object-contain rounded shadow bg-white p-2">
      @endif
      <h1 class="text-3xl font-bold text-white">{{ $team->name }}</h1>
    </div>

    <!-- Team Record -->
    <section class="flex justify-center space-x-6 mt-6">
      <div class="p-4 bg-[#1f2937] shadow rounded-lg text-center w-32 border border-[#374151]">
        <p class="text-lg font-bold text-[#84CC16]">{{ $wins }}</p>
        <p class="text-sm text-[#F3F4F6]/70">Uzvaras</p>
      </div>
      <div class="p-4 bg-[#1f2937] shadow rounded-lg text-center w-32 border border-[#374151]">
        <p class="text-lg font-bold text-[#F97316]">{{ $losses }}</p>
        <p class="text-sm text-[#F3F4F6]/70">Zaudējumi</p>
      </div>
    </section>

    <!-- Average Team Stats -->
    <section>
      <h2 class="text-2xl font-semibold text-white">Vidējie rādītāji vidējā spēlē</h2>
      <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
        @php
          $totalGames = $games->count() ?: 1;
          $stats = [
            'points' => 'Punkti',
            'oreb'   => 'Atl. bumbas uzbrukumā',
            'dreb'   => 'Atl. bumbas aizsardzībā',
            'reb'    => 'Atl. bumbas',
            'ast'    => 'Piespēles',
            'pf'     => 'Fouls',
            'tov'    => 'Kļūdas',
            'stl'    => 'Pārķertās',
            'blk'    => 'Bloķētie metieni',
            'dunk'   => 'Danki'
          ];
        @endphp
        @foreach($stats as $key => $label)
          @php
            $total = $team->players->sum(fn($p) => $p->games->sum("pivot.$key"));
            $avg = $total / $totalGames;
          @endphp
          <div class="p-4 bg-[#1f2937] shadow rounded-lg text-center border border-[#374151]">
            <p class="text-lg font-bold text-[#84CC16]">{{ number_format($avg, 1) }}</p>
            <p class="text-sm text-[#F3F4F6]/70">{{ $label }} vid. spēlē</p>
          </div>
        @endforeach
      </div>
    </section>

    <!-- Player Stats Table -->
    <section>
      <h2 class="text-2xl font-semibold text-white mt-8">Spēlētāju statistika</h2>
      <div class="overflow-x-auto mt-4 bg-[#1f2937] shadow rounded-lg border border-[#374151]">
        <table class="min-w-full divide-y divide-[#374151]">
          <thead class="bg-[#0f172a]">
            <tr>
              <th class="px-4 py-2 text-left text-xs font-medium text-[#F3F4F6]/70 uppercase">Spēlētājs</th>
              <th class="px-4 py-2 text-right text-xs font-medium text-[#F3F4F6]/70 uppercase">PPG</th>
              <th class="px-4 py-2 text-right text-xs font-medium text-[#F3F4F6]/70 uppercase">G</th>
              <th class="px-4 py-2 text-right text-xs font-medium text-[#F3F4F6]/70 uppercase">Min</th>
              <th class="px-4 py-2 text-right text-xs font-medium text-[#F3F4F6]/70 uppercase">RPG</th>
              <th class="px-4 py-2 text-right text-xs font-medium text-[#F3F4F6]/70 uppercase">APG</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#374151]">
  @foreach($team->players as $player)
    @php $gamesPlayed = $player->games->count() ?: 1; @endphp
    <tr class="hover:bg-[#2d3748] transition">
      <td class="px-4 py-2 flex items-center space-x-2 text-white">
        <a href="{{ route('lbs.player.show', $player->id) }}" class="flex items-center space-x-2 hover:text-[#84CC16]">
          @if($player->photo)
            <img src="{{ asset('storage/' . $player->photo) }}"
                 alt="{{ $player->name }}"
                 class="h-8 w-8 object-cover rounded-full border border-[#84CC16]/50">
          @endif
          <span>{{ $player->name }}</span>
        </a>
      </td>
      <td class="px-4 py-2 text-right text-[#84CC16] font-semibold">
        {{ number_format($player->games->sum('pivot.points') / $gamesPlayed, 1) }}
      </td>
      <td class="px-4 py-2 text-right text-[#F3F4F6]">
        {{ $gamesPlayed }}
      </td>
      <td class="px-4 py-2 text-right text-[#F3F4F6]">
        {{ gmdate('i:s', intval($player->games->sum(fn($g) => strtotime($g->pivot->minutes) - strtotime('00:00')) / $gamesPlayed)) }}
      </td>
      <td class="px-4 py-2 text-right text-[#F3F4F6]">
        {{ number_format($player->games->sum('pivot.reb') / $gamesPlayed, 1) }}
      </td>
      <td class="px-4 py-2 text-right text-[#F3F4F6]">
        {{ number_format($player->games->sum('pivot.ast') / $gamesPlayed, 1) }}
      </td>
    </tr>
  @endforeach
</tbody>

        </table>
      </div>
    </section>

  </main>
</body>
</html>