<!DOCTYPE html>
<html lang="lv" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $game->team1->name }} vs {{ $game->team2->name }}</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="antialiased bg-[#111827] text-[#F3F4F6]">

  <!-- Main Navbar -->
  <nav class="bg-[#111827]/80 backdrop-blur-md fixed w-full top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16 items-center">
        <div class="flex items-center space-x-4">
          <a href="{{ route('home') }}">
            <img src="{{ asset('home-icon-silhouette-svgrepo-com.svg') }}"
                 class="h-8 w-8 filter invert hover:opacity-80 transition">
          </a>
          <a href="{{ route('lbs.home') }}">
            <img src="{{ asset('415986933_1338154883529529_7481933183149808416_n.jpg') }}"
                 class="h-10">
          </a>
        </div>
        <div class="hidden md:flex space-x-6">
          @foreach(\App\Models\League::whereNull('parent_id')->get() as $league)
            <a href="{{ route('lbs.league.show', $league->id) }}"
               class="font-medium hover:text-[#84CC16] transition">
              {{ $league->name }}
            </a>
          @endforeach
        </div>
        <div class="md:hidden flex items-center">
          <button id="menu-btn" class="focus:outline-none"
                  onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
            <img src="{{ asset('burger-menu-svgrepo-com.svg') }}"
                 alt="Menu" class="h-8 w-8 filter invert">
          </button>
        </div>
      </div>
    </div>
    <div id="mobile-menu" class="hidden md:hidden bg-[#111827]/90 backdrop-blur-lg">
      <div class="space-y-2 px-4 py-3">
        @foreach(\App\Models\League::whereNull('parent_id')->get() as $league)
          <a href="{{ route('lbs.league.show', $league->id) }}"
             class="block font-medium hover:text-[#84CC16] transition">
            {{ $league->name }}
          </a>
        @endforeach
      </div>
    </div>
  </nav>

  <!-- Game Context Navbar -->
  <nav class="bg-[#0f172a]/80 backdrop-blur border-b border-white/10 fixed top-16 w-full z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex space-x-6 py-3 text-sm sm:text-base">
        <a href="{{ route('lbs.team.overview', $game->team1->id) }}"
           class="text-[#F3F4F6]/80 hover:text-[#84CC16]">{{ $game->team1->name }} PĀRSKATS</a>
        <a href="{{ route('lbs.team.overview', $game->team2->id) }}"
           class="text-[#F3F4F6]/80 hover:text-[#84CC16]">{{ $game->team2->name }} PĀRSKATS</a>
      </div>
    </div>
  </nav>

  <main class="pt-32 max-w-6xl mx-auto px-4 space-y-8">

    <div class="bg-[#1f2937] shadow rounded-lg p-6 border border-[#374151]">
      <h2 class="text-2xl font-bold mb-2 text-white">{{ $game->team1->name }} vs {{ $game->team2->name }}</h2>
      <p class="text-[#F3F4F6]/70 mb-2">Datums: {{ $game->date }}</p>
      <p class="text-xl font-semibold mb-4 text-[#84CC16]">Rezultāts: {{ $team1Score }} : {{ $team2Score }}</p>

      @if($game->team1_q1 !== null)
      <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-[#0f172a] p-3 rounded border border-[#374151]">
          <h3 class="font-semibold mb-2 text-white">{{ $game->team1->name }} ceturtdaļas</h3>
          <p>Q1: {{ $game->team1_q1 }}</p>
          <p>Q2: {{ $game->team1_q2 }}</p>
          <p>Q3: {{ $game->team1_q3 }}</p>
          <p>Q4: {{ $game->team1_q4 }}</p>
        </div>
        <div class="bg-[#0f172a] p-3 rounded border border-[#374151]">
          <h3 class="font-semibold mb-2 text-white">{{ $game->team2->name }} ceturtdaļas</h3>
          <p>Q1: {{ $game->team2_q1 }}</p>
          <p>Q2: {{ $game->team2_q2 }}</p>
          <p>Q3: {{ $game->team2_q3 }}</p>
          <p>Q4: {{ $game->team2_q4 }}</p>
        </div>
      </div>
      @endif

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach([$game->team1_id, $game->team2_id] as $teamId)
          @php
            $team = $teamId == $game->team1_id ? $game->team1 : $game->team2;
            $stats = $playerStats[$teamId] ?? collect();
            $totals = [
              'points' => $stats->sum('points'),
              'reb' => $stats->sum('reb'),
              'ast' => $stats->sum('ast'),
              'eff' => $stats->sum('eff'),
            ];
          @endphp
          <div class="bg-[#0f172a] shadow rounded-lg p-4 border border-[#374151]">
            <h2 class="text-xl font-bold mb-2 text-white">{{ $team->name }} Spēlētāju statistika</h2>
            <div class="overflow-x-auto">
              <table class="min-w-full table-auto border-collapse">
                <thead>
                  <tr class="bg-[#1f2937] text-[#F3F4F6]/80">
                    <th class="px-2 py-1 text-left">Spēlētājs</th>
                    <th class="px-2 py-1 text-right">PTS</th>
                    <th class="px-2 py-1 text-right">REB</th>
                    <th class="px-2 py-1 text-right">AST</th>
                    <th class="px-2 py-1 text-right">EFF</th>
                  </tr>
                </thead>
                <tbody>
  @foreach($stats as $stat)
    <tr class="hover:bg-[#1f2937]/70 transition">
      <td class="px-2 py-1">
        <a href="{{ route('lbs.player.show', $stat->player->id) }}" 
           class="hover:text-[#84CC16]">
          {{ $stat->player->name }}
        </a>
      </td>
      <td class="px-2 py-1 text-right text-[#84CC16]">{{ $stat->points }}</td>
      <td class="px-2 py-1 text-right">{{ $stat->reb }}</td>
      <td class="px-2 py-1 text-right">{{ $stat->ast }}</td>
      <td class="px-2 py-1 text-right">{{ $stat->eff }}</td>
    </tr>
  @endforeach
  <tr class="font-bold bg-[#1f2937]">
    <td class="px-2 py-1">Kopā</td>
    <td class="px-2 py-1 text-right text-[#84CC16]">{{ $totals['points'] }}</td>
    <td class="px-2 py-1 text-right">{{ $totals['reb'] }}</td>
    <td class="px-2 py-1 text-right">{{ $totals['ast'] }}</td>
    <td class="px-2 py-1 text-right">{{ $totals['eff'] }}</td>
  </tr>
</tbody>

              </table>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </main>

</body>
</html>
