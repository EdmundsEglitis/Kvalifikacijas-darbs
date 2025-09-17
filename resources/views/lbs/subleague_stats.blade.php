<!DOCTYPE html>
<html lang="lv" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $subLeague->name }} - Statistika</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const menuBtn = document.getElementById('menu-btn');
      const mobileMenu = document.getElementById('mobile-menu');
      if(menuBtn){
        menuBtn.addEventListener('click', () => {
          mobileMenu.classList.toggle('hidden');
        });
      }

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

  <!-- Main Navbar -->
  <nav class="bg-[#111827]/80 backdrop-blur-md fixed w-full top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16 items-center">
        <div class="flex items-center space-x-4">
          <a href="{{ route('home') }}">
            <img src="{{ asset('home-icon-silhouette-svgrepo-com.svg') }}" 
                 alt="Home" class="h-8 w-8 filter invert hover:opacity-80 transition">
          </a>
          <a href="{{ route('lbs.home') }}">
            <img src="{{ asset('415986933_1338154883529529_7481933183149808416_n.jpg') }}" 
                 alt="LBS Logo" class="h-10">
          </a>
        </div>

        <div class="hidden md:flex space-x-6">
          @foreach($parentLeagues as $league)
            <a href="{{ route('lbs.league.show', $league->id) }}" 
               class="font-medium hover:text-[#84CC16] transition">
              {{ $league->name }}
            </a>
          @endforeach
        </div>

        <div class="md:hidden flex items-center">
          <button id="menu-btn" class="focus:outline-none">
            <img src="{{ asset('burger-menu-svgrepo-com.svg') }}" alt="Menu" class="h-8 w-8 filter invert">
          </button>
        </div>
      </div>
    </div>

    <div id="mobile-menu" class="hidden md:hidden bg-[#111827]/90 backdrop-blur-lg">
      <div class="space-y-2 px-4 py-3">
        @foreach($parentLeagues as $league)
          <a href="{{ route('lbs.league.show', $league->id) }}" 
             class="block font-medium hover:text-[#84CC16] transition">
            {{ $league->name }}
          </a>
        @endforeach
      </div>
    </div>
  </nav>

  <!-- Sub-League Tabs Navbar -->
  <nav class="bg-[#0f172a]/80 backdrop-blur border-b border-white/10 fixed top-16 w-full z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex space-x-6 py-3 text-sm sm:text-base">
        <a href="{{ route('lbs.subleague.news', $subLeague->id) }}" class="hover:text-[#84CC16] text-[#F3F4F6]/80">JAUNUMI</a>
        <a href="{{ route('lbs.subleague.calendar', $subLeague->id) }}" class="hover:text-[#84CC16] text-[#F3F4F6]/80">KALENDĀRS</a>
        <a href="{{ route('lbs.subleague.teams', $subLeague->id) }}" class="hover:text-[#84CC16] text-[#F3F4F6]/80">KOMANDAS</a>
        <a href="{{ route('lbs.subleague.stats', $subLeague->id) }}" class="text-[#84CC16] font-bold border-b-2 border-[#84CC16]">STATISTIKA</a>
      </div>
    </div>
  </nav>

  <!-- Stats Section Navbar -->
  <nav class="bg-[#1f2937] fixed top-28 w-full z-30 shadow border-b border-[#374151]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex space-x-6 py-2 text-sm sm:text-base">
        <a href="#teams" class="text-[#84CC16] font-bold">Komandu statistika</a>
        <a href="#top-players" class="hover:text-[#84CC16] text-[#F3F4F6]/80">Top spēlētāji</a>
        <a href="#all-players" class="hover:text-[#84CC16] text-[#F3F4F6]/80">Spēlētāji</a>
      </div>
    </div>
  </nav>

  <!-- Page Content -->
  <main class="pt-44 max-w-7xl mx-auto px-4 space-y-16">

    <!-- Team Stats -->
    <section id="teams">
      <h1 class="text-3xl font-bold text-white mb-6">{{ $subLeague->name }} - Komandu statistika</h1>
      @php $sortedTeams = $teamsStats->sortByDesc('wins'); @endphp
      @foreach($sortedTeams as $teamStat)
        <div class="bg-[#1f2937] shadow rounded-lg p-6 mb-4 border border-[#374151] hover:border-[#84CC16] transition">
          <h2 class="text-xl font-semibold text-white">{{ $teamStat['team']->name }}</h2>
          <p class="mt-2 text-[#84CC16] font-bold">Uzvaras: {{ $teamStat['wins'] }}</p>
          <p class="text-[#F97316] font-bold">Zaudējumi: {{ $teamStat['losses'] }}</p>
        </div>
      @endforeach
    </section>

    <!-- Top Players -->
    <section id="top-players">
      <h2 class="text-2xl font-bold text-white mb-6">Top spēlētāji</h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($topPlayers as $stat => $player)
          <div class="bg-[#1f2937] shadow rounded-lg p-6 text-center border border-[#374151] hover:border-[#84CC16] transition">
            <h3 class="text-lg font-semibold capitalize text-[#F3F4F6]">{{ $stat }}</h3>
            <p class="mt-2 text-white font-bold">{{ $player->name }}</p>
            <p class="text-[#84CC16]">Vidēji: {{ $player->avg_value }}</p>
          </div>
        @endforeach
      </div>
    </section>

<!-- All Players -->
<section id="all-players">
  <h2 class="text-2xl font-bold text-[#84CC16] mb-6">Visi spēlētāji</h2>

  <input
    id="player-search"
    type="text"
    placeholder="Meklēt spēlētāju..."
    class="mb-4 w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#84CC16] focus:border-[#84CC16] placeholder-gray-400"
  />

  <div class="overflow-x-auto rounded-lg border border-gray-200 shadow">
    <table id="players-table" class="min-w-[720px] sm:min-w-full bg-white">
      <thead class="bg-gray-100 sticky top-0 z-10">
        <tr>
          <th data-sort class="px-3 sm:px-4 py-2 text-left text-xs sm:text-sm font-semibold text-gray-700 cursor-pointer select-none">
            Spēlētājs
          </th>
          <th data-sort class="px-3 sm:px-4 py-2 text-left text-xs sm:text-sm font-semibold text-gray-700 cursor-pointer select-none">
            Komanda
          </th>
          <th data-sort class="px-3 sm:px-4 py-2 text-right text-xs sm:text-sm font-semibold text-gray-700 cursor-pointer select-none">
            Punkti AVG
          </th>
          <th data-sort class="px-3 sm:px-4 py-2 text-right text-xs sm:text-sm font-semibold text-gray-700 cursor-pointer select-none">
            Atlēkušās AVG
          </th>
          <th data-sort class="px-3 sm:px-4 py-2 text-right text-xs sm:text-sm font-semibold text-gray-700 cursor-pointer select-none">
            Rezultīvās piespēles AVG
          </th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200">
        @foreach($playersStats as $player)
          <tr class="odd:bg-white even:bg-gray-50 hover:bg-gray-100 transition-colors">
            <td class="px-3 sm:px-4 py-2 text-sm text-gray-800">
              {{ $player->name }}
            </td>
            <td class="px-3 sm:px-4 py-2 text-sm text-gray-700">
              {{ $player->team->name }}
            </td>
            <td class="px-3 sm:px-4 py-2 text-sm text-gray-900 text-right whitespace-nowrap">
              {{ $player->avg_points }}
            </td>
            <td class="px-3 sm:px-4 py-2 text-sm text-gray-900 text-right whitespace-nowrap">
              {{ $player->avg_rebounds }}
            </td>
            <td class="px-3 sm:px-4 py-2 text-sm text-gray-900 text-right whitespace-nowrap">
              {{ $player->avg_assists }}
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <!-- Small helper text for mobile users -->
  <p class="mt-2 text-xs text-gray-500 sm:hidden">Velc tabulu horizontāli, lai redzētu visas kolonnas.</p>
</section>
