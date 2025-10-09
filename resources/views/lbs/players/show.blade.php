@extends('layouts.app')
@section('title', $player->name . ' – Spēlētāja profils')

{{-- Subnav: team tabs below the main navbar (only if the player has a team) --}}
@section('subnav')
  @if(!empty($player->team))
    <x-teamnav :team="$player->team" />
  @endif
@endsection

@section('content')
  @php
    // Safe defaults for totals/averages
    $games = (int)($totals['games'] ?? 0);
    $avg   = fn($k) => isset($averages[$k]) ? $averages[$k] : '—';
  @endphp

  <br>

  <div class="max-w-6xl mx-auto px-4 space-y-12 sm:space-y-16">

    {{-- Header --}}
    <section class="grid grid-cols-1 md:grid-cols-[auto,1fr] items-start gap-6 md:gap-8">
      <div class="flex md:block justify-center">
        @if(!empty($player->photo))
          <img
            src="{{ asset('storage/' . $player->photo) }}"
            alt="{{ $player->name }}"
            class="h-28 w-28 sm:h-36 sm:w-36 rounded-full border-4 border-[#84CC16]/60 shadow-lg object-cover"
          >
        @else
          <div class="h-28 w-28 sm:h-36 sm:w-36 rounded-full bg-gray-700 flex items-center justify-center text-gray-400 text-xs sm:text-sm">
            No Photo
          </div>
        @endif
      </div>

      <div class="space-y-4 text-center md:text-left">
        <div>
          <h1 class="text-2xl sm:text-4xl font-extrabold text-white leading-tight">{{ $player->name }}</h1>
          @if(!empty($player->team))
            <p class="mt-1 text-[#9CA3AF]">
              Komanda:
              <a href="{{ route('lbs.team.show', $player->team->id) }}"
                 class="text-[#84CC16] hover:underline font-medium">
                {{ $player->team->name }}
              </a>
            </p>
          @endif
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

    {{-- Season summary --}}
    <section>
      <h2 class="text-xl sm:text-2xl font-bold text-white mb-4 sm:mb-6">Sezonas kopsavilkums</h2>

      @if($games > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
          @foreach([
            ['label' => 'Spēles',            'value' => $games],
            ['label' => 'Vid. punkti',       'value' => $avg('points')],
            ['label' => 'Vid. atl. bumbas',  'value' => $avg('reb')],
            ['label' => 'Vid. piespēles',    'value' => $avg('ast')],
            ['label' => 'Vid. pārtvertās',   'value' => $avg('stl')],
            ['label' => 'Vid. efektivitāte', 'value' => $avg('eff')],
          ] as $stat)
            <div class="bg-[#1f2937] p-4 sm:p-5 rounded-xl text-center shadow border border-[#374151] hover:border-[#84CC16] transition">
              <div class="text-xs sm:text-sm text-[#9CA3AF]">{{ $stat['label'] }}</div>
              <div class="mt-0.5 text-xl sm:text-2xl font-extrabold text-[#84CC16]">
                {{ is_numeric($stat['value']) ? (str_contains((string)$stat['value'], '.') ? number_format((float)$stat['value'], 1) : $stat['value']) : $stat['value'] }}
              </div>
            </div>
          @endforeach
        </div>
      @else
        <p class="text-[#F3F4F6]/70">Nav pieejamas statistikas.</p>
      @endif
    </section>

    {{-- Game-by-game stats --}}
    <section>
      <h2 class="text-xl sm:text-2xl font-bold text-white mb-4 sm:mb-6">Spēļu statistika</h2>

      {{-- Mobile cards --}}
      <div class="space-y-3 sm:space-y-4 md:hidden">
        @foreach($player->playerGameStats as $stat)
          @php
            $g = $stat->game ?? null;
            if (!$g) continue;
            $opponent = ($g->team1?->id ?? null) === ($player->team_id ?? null) ? ($g->team2 ?? null) : ($g->team1 ?? null);
          @endphp
          <article class="bg-[#1f2937] border border-[#374151] rounded-xl p-4 shadow hover:shadow-lg transition">
            <div class="flex items-start justify-between gap-3">
              <div>
                <div class="text-sm text-gray-300">{{ optional($g->date)->format('d.m.Y') }}</div>
                @if($opponent)
                  <a href="{{ route('lbs.team.show', $opponent->id) }}"
                     class="block text-base font-semibold text-white hover:text-[#84CC16] truncate">
                    {{ $opponent->name }}
                  </a>
                @endif
              </div>
              <div class="text-right">
                <div class="text-xs text-gray-400">Punkti</div>
                <div class="text-lg font-bold text-[#84CC16]">{{ $stat->points ?? '—' }}</div>
              </div>
            </div>

            <dl class="mt-3 grid grid-cols-3 gap-2 text-xs">
              @foreach([
                ['Min',   $stat->minutes ?? '—'],
                ['2PM/2PA', ($stat->fgm2 ?? '—') . '/' . ($stat->fga2 ?? '—')],
                ['3PM/3PA', ($stat->fgm3 ?? '—') . '/' . ($stat->fga3 ?? '—')],
                ['FTM/FTA', ($stat->ftm  ?? '—') . '/' . ($stat->fta  ?? '—')],
                ['REB',  $stat->reb ?? '—'],
                ['AST',  $stat->ast ?? '—'],
                ['STL',  $stat->stl ?? '—'],
                ['BLK',  $stat->blk ?? '—'],
                ['TOV',  $stat->tov ?? '—'],
                ['PF',   $stat->pf  ?? '—'],
              ] as [$label,$val])
                <div class="rounded-lg bg-white/5 border border-white/10 p-2 text-center">
                  <dt class="text-gray-400">{{ $label }}</dt>
                  <dd class="font-semibold">{{ $val }}</dd>
                </div>
              @endforeach
              <div class="rounded-lg bg-white/5 border border-white/10 p-2 text-center col-span-3">
                <dt class="text-gray-400">EFF</dt><dd class="font-semibold">{{ $stat->eff ?? '—' }}</dd>
              </div>
            </dl>

            {{-- Link to game details --}}
            @if($g?->id)
              <a href="{{ route('lbs.game.detail', $g->id) }}"
                 class="mt-3 inline-flex items-center gap-2 text-[#84CC16] hover:underline">
                Skatīt spēles detaļas →
              </a>
            @endif
          </article>
        @endforeach
      </div>

      {{-- Desktop table --}}
      <div class="hidden md:block overflow-x-auto rounded-xl shadow-lg border border-[#374151]">
        <table id="playerStatsTable" class="min-w-full divide-y divide-[#374151]">
          <thead class="bg-[#0f172a] sticky top-0 z-10 select-none">
            <tr>
              @php
                // [label, type]
                $cols = [
                  ['Datums','date'],
                  ['Pretinieks','text'],
                  ['Min','time'],
                  ['Punkti','number'],
                  ['2PM/2PA','ratio'],
                  ['3PM/3PA','ratio'],
                  ['FTM/FTA','ratio'],
                  ['REB','number'],
                  ['AST','number'],
                  ['STL','number'],
                  ['BLK','number'],
                  ['TOV','number'],
                  ['PF','number'],
                  ['EFF','number'],
                ];
              @endphp
              @foreach($cols as [$label,$type])
                <th
                  class="px-3 py-3 text-xs font-semibold text-[#F3F4F6]/70 uppercase text-center cursor-pointer hover:text-white"
                  data-sort-type="{{ $type }}"
                  aria-sort="none"
                >
                  <span class="inline-flex items-center gap-1">
                    {{ $label }} <span class="opacity-60 sort-caret">↕</span>
                  </span>
                </th>
              @endforeach
            </tr>
          </thead>
          <tbody class="divide-y divide-[#374151]">
            @foreach($player->playerGameStats as $stat)
              @php
                $g = $stat->game ?? null;
                if (!$g) continue;
                $opponent = ($g->team1?->id ?? null) === ($player->team_id ?? null) ? ($g->team2 ?? null) : ($g->team1 ?? null);
              @endphp
              <tr class="hover:bg-[#223041] transition">
                {{-- Datums (d.m.Y) --}}
                <td class="px-3 py-2 text-sm text-center whitespace-nowrap">
                  <a href="{{ $g?->id ? route('lbs.game.detail', $g->id) : '#' }}"
                     class="hover:underline">
                    {{ optional($g->date)->format('d.m.Y') }}
                  </a>
                </td>

                {{-- Pretinieks --}}
                <td class="px-3 py-2 text-sm text-center">
                  @if($opponent)
                    <a href="{{ route('lbs.team.show', $opponent->id) }}" class="hover:text-[#84CC16] font-medium">
                      {{ $opponent->name }}
                    </a>
                  @else
                    —
                  @endif
                </td>

                {{-- Min --}}
                <td class="px-3 py-2 text-center">{{ $stat->minutes ?? '—' }}</td>

                {{-- Punkți --}}
                <td class="px-3 py-2 text-center font-semibold text-[#84CC16]">{{ $stat->points ?? '—' }}</td>

                {{-- 2PM/2PA --}}
                <td class="px-3 py-2 text-center">{{ ($stat->fgm2 ?? '—') }}/{{ ($stat->fga2 ?? '—') }}</td>

                {{-- 3PM/3PA --}}
                <td class="px-3 py-2 text-center">{{ ($stat->fgm3 ?? '—') }}/{{ ($stat->fga3 ?? '—') }}</td>

                {{-- FTM/FTA --}}
                <td class="px-3 py-2 text-center">{{ ($stat->ftm  ?? '—') }}/{{ ($stat->fta  ?? '—') }}</td>

                {{-- Rebounds / others --}}
                <td class="px-3 py-2 text-center">{{ $stat->reb ?? '—' }}</td>
                <td class="px-3 py-2 text-center">{{ $stat->ast ?? '—' }}</td>
                <td class="px-3 py-2 text-center">{{ $stat->stl ?? '—' }}</td>
                <td class="px-3 py-2 text-center">{{ $stat->blk ?? '—' }}</td>
                <td class="px-3 py-2 text-center">{{ $stat->tov ?? '—' }}</td>
                <td class="px-3 py-2 text-center">{{ $stat->pf  ?? '—' }}</td>
                <td class="px-3 py-2 text-center font-semibold">{{ $stat->eff ?? '—' }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </section>

  </div>

  {{-- Sorter --}}
<script>
  (function () {
    const table  = document.getElementById('playerStatsTable');
    if (!table) return;

    const thead  = table.tHead;
    const tbody  = table.tBodies[0];
    if (!thead || !tbody) return;

    // Keep a copy of original row order (used when switching columns)
    const originalRows = Array.from(tbody.rows);

    const parse = {
      number: (s) => {
        const n = parseFloat(String(s).replace(/[^\d.-]/g, ''));
        return isNaN(n) ? -Infinity : n;
      },
      text: (s) => String(s).toLowerCase(),
      date: (s) => { // expects dd.mm.yyyy
        const m = String(s).trim().match(/^(\d{2})\.(\d{2})\.(\d{4})$/);
        if (!m) return -Infinity;
        return new Date(`${m[3]}-${m[2]}-${m[1]}`).getTime();
      },
      time: (s) => { // mm:ss
        const m = String(s).trim().match(/^(\d+):(\d{2})$/);
        if (!m) return -Infinity;
        return parseInt(m[1],10) * 60 + parseInt(m[2],10);
      },
      ratio: (s) => { // x/y -> % made (fallback to made when y=0)
        const m = String(s).trim().match(/^(\d+)\s*\/\s*(\d+)$/);
        if (!m) return -Infinity;
        const made = parseInt(m[1],10), att = parseInt(m[2],10);
        if (att === 0) return made === 0 ? -Infinity : Infinity;
        return made / att;
      }
    };

    function getCellText(row, idx) {
      const cell = row.cells[idx];
      return cell ? (cell.innerText || cell.textContent || '') : '';
    }

    function clearOtherHeaders(activeTh) {
      Array.from(thead.querySelectorAll('th[aria-sort]')).forEach(th => {
        if (th === activeTh) return;
        th.setAttribute('aria-sort', 'none');
        const caret = th.querySelector('.sort-caret');
        if (caret) caret.textContent = '↕';
      });
    }

    function setCaret(th, dir) {
      const caret = th.querySelector('.sort-caret');
      if (!caret) return;
      caret.textContent = dir === 'asc' ? '↑' : '↓';
    }

    // Remember which column is currently sorted
    let activeCol = -1;

    thead.addEventListener('click', (e) => {
      const th = e.target.closest('th[data-sort-type]');
      if (!th) return;

      const colIndex = Array.from(th.parentNode.children).indexOf(th);
      const type = th.getAttribute('data-sort-type') || 'text';

      // If switching to a new column, start from original order and default to ASC
      if (colIndex !== activeCol) {
        tbody.replaceChildren(...originalRows);
        activeCol = colIndex;
        th.setAttribute('aria-sort', 'desc'); // will flip to 'asc' immediately below
      }

      // Toggle only between ASC and DESC (no neutral)
      const curr = th.getAttribute('aria-sort');
      const next = curr === 'asc' ? 'desc' : 'asc';

      clearOtherHeaders(th);
      th.setAttribute('aria-sort', next);
      setCaret(th, next);

      const rows = Array.from(tbody.rows);
      const cmp = (a, b) => {
        const va = parse[type](getCellText(a, colIndex));
        const vb = parse[type](getCellText(b, colIndex));
        if (va < vb) return next === 'asc' ? -1 : 1;
        if (va > vb) return next === 'asc' ? 1 : -1;
        return 0;
      };

      rows.sort(cmp);
      tbody.replaceChildren(...rows);
    });
  })();
</script>

@endsection
