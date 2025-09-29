<!DOCTYPE html>
<html lang="lv" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $subLeague->name }} – Statistika</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Sorting
      document.querySelectorAll('[data-sort]').forEach(header => {
        header.addEventListener('click', () => {
          const table = header.closest('table');
          const rows = Array.from(table.querySelector('tbody').rows);
          const idx = header.cellIndex;
          const asc = header.dataset.asc === 'true' ? false : true;
          header.dataset.asc = asc;

          rows.sort((a, b) => {
            let v1 = a.cells[idx].innerText;
            let v2 = b.cells[idx].innerText;
            if(!isNaN(v1) && !isNaN(v2)) {
              v1 = parseFloat(v1); v2 = parseFloat(v2);
            }
            return asc ? v1 > v2 ? 1 : -1 : v1 < v2 ? 1 : -1;
          });

          table.querySelector('tbody').append(...rows);
        });
      });

      // Search filter
      const searchInput = document.getElementById('player-search');
      if (searchInput) {
        searchInput.addEventListener('input', (e) => {
          const term = e.target.value.toLowerCase();
          document.querySelectorAll('#players-table tbody tr').forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(term) ? '' : 'none';
          });
        });
      }
    });
  </script>
</head>
<body class="antialiased bg-[#111827] text-[#F3F4F6]">

  {{-- Main + Sub tabs --}}
  <x-sub-league-tabs :parentLeagues="$parentLeagues" :subLeague="$subLeague" />

  {{-- Stats Section Navbar --}}
  <nav class="bg-[#1f2937]/80 backdrop-blur-md fixed top-28 w-full z-30 border-b border-[#374151]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex space-x-6 py-3 text-sm sm:text-base font-medium">
        <a href="#teams" class="text-[#84CC16]">Komandu statistika</a>
        <a href="#top-players" class="hover:text-[#84CC16] text-[#F3F4F6]/80">Top spēlētāji</a>
        <a href="#all-players" class="hover:text-[#84CC16] text-[#F3F4F6]/80">Spēlētāji</a>
      </div>
    </div>
  </nav>

  {{-- Page Content --}}
  <main class="pt-44 max-w-7xl mx-auto px-4 space-y-20">

    {{-- Team Stats --}}
    <section id="teams">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-extrabold text-white">{{ $subLeague->name }} — Komandu statistika</h1>
        <span class="px-3 py-1 rounded-full bg-[#84CC16]/20 text-[#84CC16] text-xs">
          {{ $teamsStats->count() }} komandas
        </span>
      </div>

      @php $sortedTeams = $teamsStats->sortByDesc('wins'); @endphp
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($sortedTeams as $teamStat)
          <div class="bg-[#1f2937] border border-[#374151] hover:border-[#84CC16] rounded-xl p-6 shadow transition">
            <h2 class="text-lg font-semibold text-white">{{ $teamStat['team']->name }}</h2>
            <p class="mt-2 text-[#84CC16] font-bold">Uzvaras: {{ $teamStat['wins'] }}</p>
            <p class="text-[#F97316] font-bold">Zaudējumi: {{ $teamStat['losses'] }}</p>
          </div>
        @endforeach
      </div>
    </section>

    {{-- Top Players --}}
    <section id="top-players">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-white">Top spēlētāji</h2>
        <span class="px-3 py-1 rounded-full bg-white/10 text-white text-xs">
          {{ count($topPlayers) }}
        </span>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($topPlayers as $stat => $player)
          <div class="bg-[#1f2937] border border-[#374151] hover:border-[#84CC16] rounded-xl p-6 text-center shadow transition">
            <h3 class="text-sm uppercase font-semibold text-[#F3F4F6]/80">{{ $stat }}</h3>
            <a href="{{ route('lbs.player.show', $player->id) }}"
               class="mt-3 block text-lg font-bold text-white hover:text-[#84CC16] transition">
              {{ $player->name }}
            </a>
            <p class="text-[#84CC16] font-semibold">Vidēji: {{ $player->avg_value }}</p>
          </div>
        @endforeach
      </div>
    </section>

    {{-- All Players --}}
    <section id="all-players">
      <h2 class="text-2xl font-bold text-white mb-6">Visi spēlētāji</h2>

      <input
        id="player-search"
        type="text"
        placeholder="🔍 Meklēt spēlētāju..."
        class="mb-6 w-full rounded-lg px-4 py-2 bg-[#1f2937] text-white placeholder-gray-400 border border-[#374151] focus:outline-none focus:ring-2 focus:ring-[#84CC16] focus:border-[#84CC16]"
      />

      <div class="overflow-x-auto rounded-lg border border-[#374151] shadow">
        <table id="players-table" class="min-w-[720px] sm:min-w-full">
          <thead class="bg-[#1f2937] text-[#F3F4F6]/80 text-xs uppercase sticky top-0 z-10">
            <tr>
              <th data-sort class="px-4 py-3 text-left font-semibold cursor-pointer">Spēlētājs</th>
              <th data-sort class="px-4 py-3 text-left font-semibold cursor-pointer">Komanda</th>
              <th data-sort class="px-4 py-3 text-right font-semibold cursor-pointer">Punkti AVG</th>
              <th data-sort class="px-4 py-3 text-right font-semibold cursor-pointer">Atlēkušās AVG</th>
              <th data-sort class="px-4 py-3 text-right font-semibold cursor-pointer">Piespēles AVG</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#374151] bg-[#111827]">
            @foreach($playersStats as $player)
              <tr class="hover:bg-[#1f2937] transition">
                <td class="px-4 py-3">
                  <a href="{{ route('lbs.player.show', $player->id) }}" class="hover:text-[#84CC16]">
                    {{ $player->name }}
                  </a>
                </td>
                <td class="px-4 py-3">
                  <a href="{{ route('lbs.team.show', $player->team->id) }}" class="hover:text-[#84CC16]">
                    {{ $player->team->name }}
                  </a>
                </td>
                <td class="px-4 py-3 text-right font-medium">{{ $player->avg_points }}</td>
                <td class="px-4 py-3 text-right font-medium">{{ $player->avg_rebounds }}</td>
                <td class="px-4 py-3 text-right font-medium">{{ $player->avg_assists }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <p class="mt-3 text-xs text-gray-400 sm:hidden">👉 Velc tabulu horizontāli, lai redzētu visas kolonnas.</p>
    </section>

  </main>
</body>
</html>
