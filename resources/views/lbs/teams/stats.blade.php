<!DOCTYPE html>
<html lang="lv" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $team->name }} – Komandas statistika</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="antialiased bg-[#111827] text-[#F3F4F6]">

  <x-team-navbar :parentLeagues="$parentLeagues" :team="$team" />

  <main class="pt-32 max-w-7xl mx-auto px-4 space-y-16">

    <!-- Team Logo + Name -->
    <div class="flex flex-col items-center space-y-4 text-center">
      @if($team->logo)
        <img src="{{ asset('storage/' . $team->logo) }}"
             alt="{{ $team->name }}"
             class="h-28 w-28 object-contain shadow-lg bg-[#111827] rounded-xl ring-2 ring-[#84CC16]/40">
      @endif
      <h1 class="text-4xl font-extrabold text-white drop-shadow">{{ $team->name }}</h1>
      <p class="text-[#F3F4F6]/70 text-sm">Komandas pārskats & statistika</p>
    </div>

    <!-- Team Record -->
    <section>
      <h2 class="text-2xl font-bold text-white mb-6">Komandas Rezultāti
      <div class="flex justify-center gap-6">
        <div class="p-6 bg-[#1f2937] rounded-xl shadow-lg text-center w-40 border border-[#374151] hover:border-[#84CC16] transition">
          <p class="text-3xl font-extrabold text-[#84CC16]">{{ $wins }}</p>
          <p class="mt-1 text-sm text-[#F3F4F6]/80">Uzvaras</p>
        </div>
        <div class="p-6 bg-[#1f2937] rounded-xl shadow-lg text-center w-40 border border-[#374151] hover:border-[#F97316] transition">
          <p class="text-3xl font-extrabold text-[#F97316]">{{ $losses }}</p>
          <p class="mt-1 text-sm text-[#F3F4F6]/80">Zaudējumi</p>
        </div>
      </div>
    </section>

    <!-- Average Team Stats -->
    <section>
      <h2 class="text-2xl font-bold text-white mb-6">Vidējie rādītāji vidējā spēlē</h2>
      <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-6">
        @foreach($averageStats as $stat)
          <div class="p-5 bg-[#1f2937] rounded-xl shadow border border-[#374151] hover:border-[#84CC16] transition flex flex-col items-center">
            <p class="text-2xl font-bold text-[#84CC16]">{{ number_format($stat['avg'], 1) }}</p>
            <p class="text-sm text-[#F3F4F6]/70 mt-1">{{ $stat['label'] }}</p>
          </div>
        @endforeach
      </div>
    </section>

    <!-- Player Stats Table -->
    <section>
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-white">Spēlētāju statistika</h2>
        <span class="px-3 py-1 text-xs rounded-full bg-[#84CC16]/20 text-[#84CC16] font-semibold">
          {{ $playersStats->count() }} spēlētāji
        </span>
      </div>

      <div class="overflow-x-auto rounded-xl shadow-lg border border-[#374151] bg-[#1f2937]">
        <table class="min-w-full divide-y divide-[#374151]">
          <thead class="bg-[#0f172a] sticky top-0 z-10">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-semibold text-[#F3F4F6]/70 uppercase">Spēlētājs</th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-[#F3F4F6]/70 uppercase">PPG</th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-[#F3F4F6]/70 uppercase">G</th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-[#F3F4F6]/70 uppercase">Min</th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-[#F3F4F6]/70 uppercase">RPG</th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-[#F3F4F6]/70 uppercase">APG</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#374151]">
            @foreach($playersStats as $player)
              <tr class="hover:bg-[#2d3748] transition">
                <td class="px-4 py-3 flex items-center space-x-3 text-white">
                  <a href="{{ route('lbs.player.show', $player['id']) }}" class="flex items-center space-x-3 hover:text-[#84CC16]">
                    @if($player['photo'])
                      <img src="{{ asset('storage/' . $player['photo']) }}"
                           alt="{{ $player['name'] }}"
                           class="h-9 w-9 object-cover rounded-full border border-[#84CC16]/40">
                    @else
                      <div class="h-9 w-9 rounded-full bg-gray-600 flex items-center justify-center text-xs text-gray-300">?</div>
                    @endif
                    <span class="font-medium">{{ $player['name'] }}</span>
                  </a>
                </td>
                <td class="px-4 py-3 text-right text-[#84CC16] font-semibold">{{ number_format($player['ppg'], 1) }}</td>
                <td class="px-4 py-3 text-right">{{ $player['gamesPlayed'] }}</td>
                <td class="px-4 py-3 text-right">{{ gmdate('i:s', intval($player['minutes'])) }}</td>
                <td class="px-4 py-3 text-right">{{ number_format($player['rpg'], 1) }}</td>
                <td class="px-4 py-3 text-right">{{ number_format($player['apg'], 1) }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

    </section>

  </main>
</body>
</html>
