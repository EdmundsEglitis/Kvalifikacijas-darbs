@extends('layouts.app')
@section('title', $subLeague->name . ' – Statistika')

@section('subnav')
  <x-lbs-subnav :subLeague="$subLeague" />
@endsection

@section('content')
  <nav class="sticky top-28 z-30 bg-transparent backdrop-blur-md border-t border-[#374151]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex space-x-6 py-3 text-sm sm:text-base font-medium">
        <a href="#teams" class="text-[#84CC16]">Komandu statistika</a>
        <a href="#top-players" class="hover:text-[#84CC16] text-[#F3F4F6]/80">Top spēlētāji</a>
        <a href="#all-players" class="hover:text-[#84CC16] text-[#F3F4F6]/80">Spēlētāji</a>
      </div>
    </div>
  </nav>

  <div class="max-w-7xl mx-auto px-4 space-y-20 pt-6">
    <section id="teams">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-extrabold text-white">
          {{ $subLeague->name }} — Komandu statistika
        </h1>
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

    <section id="top-players">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-white">Top spēlētāji</h2>
        <span class="px-3 py-1 rounded-full bg-white/10 text-white text-xs">
          {{ is_countable($topPlayers) ? count($topPlayers) : $topPlayers->count() }}
        </span>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($topPlayers as $stat => $player)
          @if($player)
            <div class="bg-[#1f2937] border border-[#374151] hover:border-[#84CC16] rounded-xl p-6 text-center shadow transition">
              <h3 class="text-sm uppercase font-semibold text-[#F3F4F6]/80">{{ $stat }}</h3>
              <a href="{{ route('lbs.player.show', $player->id) }}"
                 class="mt-3 block text-lg font-bold text-white hover:text-[#84CC16] transition">
                {{ $player->name }}
              </a>
              <p class="text-[#84CC16] font-semibold">Vidēji: {{ $player->avg_value }}</p>
            </div>
          @endif
        @endforeach
      </div>
    </section>

    {{-- ALL PLAYERS (sortable + searchable) --}}
    <section id="all-players">
      <h2 class="text-2xl font-bold text-white mb-6">Visi spēlētāji</h2>

      <input
        id="player-search"
        type="text"
        placeholder="🔍 Meklēt spēlētāju..."
        class="mb-6 w-full rounded-lg px-4 py-2 bg-[#1f2937] text-white placeholder-gray-400 border border-[#374151] focus:outline-none focus:ring-2 focus:ring-[#84CC16] focus:border-[#84CC16]"
      />

      <div class="overflow-x-auto rounded-lg border border-[#374151] shadow bg-[#1f2937]">
        <table id="players-table" class="min-w-[720px] sm:min-w-full">
          <thead class="bg-[#0f172a] text-[#F3F4F6]/80 text-xs uppercase sticky top-0 z-10 select-none">
            <tr>
              {{-- data-sort-type controls parsing, caret shows direction --}}
              <th class="px-4 py-3 text-left font-semibold cursor-pointer"
                  data-sort-type="text" aria-sort="none">
                <span class="inline-flex items-center gap-1">Spēlētājs <span class="sort-caret opacity-60">↕</span></span>
              </th>
              <th class="px-4 py-3 text-left font-semibold cursor-pointer"
                  data-sort-type="text" aria-sort="none">
                <span class="inline-flex items-center gap-1">Komanda <span class="sort-caret opacity-60">↕</span></span>
              </th>
              <th class="px-4 py-3 text-right font-semibold cursor-pointer"
                  data-sort-type="number" aria-sort="none">
                <span class="inline-flex items-center gap-1">Punkti AVG <span class="sort-caret opacity-60">↕</span></span>
              </th>
              <th class="px-4 py-3 text-right font-semibold cursor-pointer"
                  data-sort-type="number" aria-sort="none">
                <span class="inline-flex items-center gap-1">Atlēkušās AVG <span class="sort-caret opacity-60">↕</span></span>
              </th>
              <th class="px-4 py-3 text-right font-semibold cursor-pointer"
                  data-sort-type="number" aria-sort="none">
                <span class="inline-flex items-center gap-1">Piespēles AVG <span class="sort-caret opacity-60">↕</span></span>
              </th>
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
                  @if(optional($player->team)->id)
                    <a href="{{ route('lbs.team.show', $player->team->id) }}" class="hover:text-[#84CC16]">
                      {{ $player->team->name }}
                    </a>
                  @else
                    <span class="text-[#F3F4F6]/60">—</span>
                  @endif
                </td>
                <td class="px-4 py-3 text-right font-medium tabular-nums">{{ $player->avg_points }}</td>
                <td class="px-4 py-3 text-right font-medium tabular-nums">{{ $player->avg_rebounds }}</td>
                <td class="px-4 py-3 text-right font-medium tabular-nums">{{ $player->avg_assists }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <p class="mt-3 text-xs text-gray-400 sm:hidden">👉 Velc tabulu horizontāli, lai redzētu visas kolonnas.</p>
    </section>
  </div>
@endsection

@push('scripts')
<script>
(function(){
  const table = document.getElementById('players-table');
  if (!table) return;

  const thead = table.tHead;
  const tbody = table.tBodies[0];
  if (!thead || !tbody) return;

  const dirMap = new Map();

  const parse = {
    number: (s) => {
      const n = parseFloat(String(s).replace(/[^\d.\-]/g,'').replace(',', '.'));
      return isNaN(n) ? -Infinity : n;
    },
    text: (s) => String(s).toLowerCase()
  };

  function getColIndex(th){
    const cells = Array.from(th.parentElement.children);
    return cells.indexOf(th);
  }

  function clearOtherCarets(activeTh){
    thead.querySelectorAll('th[aria-sort]').forEach(th=>{
      if (th === activeTh) return;
      th.setAttribute('aria-sort', 'none');
      const c = th.querySelector('.sort-caret');
      if (c) c.textContent = '↕';
    });
  }

  function setCaret(th, asc){
    th.setAttribute('aria-sort', asc ? 'ascending' : 'descending');
    const c = th.querySelector('.sort-caret');
    if (c) c.textContent = asc ? '↑' : '↓';
  }

  thead.addEventListener('click', (e)=>{
    const th = e.target.closest('th[data-sort-type]');
    if (!th) return;

    const col = getColIndex(th);
    const type = th.getAttribute('data-sort-type') || 'text';

    const nextAsc = !dirMap.get(col);
    dirMap.set(col, nextAsc);

    clearOtherCarets(th);
    setCaret(th, nextAsc);

    const rows = Array.from(tbody.rows);
    rows.sort((a,b)=>{
      const ta = a.cells[col]?.textContent ?? '';
      const tb = b.cells[col]?.textContent ?? '';
      const va = parse[type](ta);
      const vb = parse[type](tb);
      if (va < vb) return nextAsc ? -1 : 1;
      if (va > vb) return nextAsc ?  1 : -1;
      return 0;
    });

    const frag = document.createDocumentFragment();
    rows.forEach(r => frag.appendChild(r));
    tbody.appendChild(frag);
  }, false);

  // Search filter
  const searchInput = document.getElementById('player-search');
  if (searchInput) {
    searchInput.addEventListener('input', (e) => {
      const term = e.target.value.toLowerCase();
      Array.from(tbody.rows).forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(term) ? '' : 'none';
      });
    });
  }
})();
</script>
@endpush
