
@extends('layouts.nba')
@section('title','Team Compare')

@section('content')
<br><br>
<br><br>
  <main class="max-w-7xl mx-auto px-4 py-6 space-y-8">

    {{-- Filters --}}
    <section class="bg-[#1f2937] border border-[#374151] rounded-xl p-4 sm:p-5">
      <form class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 items-end" method="GET">
        <div>
          <label class="block text-xs text-gray-400 mb-1">From season</label>
          <select name="from" class="w-full bg-[#0f172a] border border-[#374151] rounded-lg px-3 py-2 focus:outline-none">
            @foreach($seasons as $s)
              <option value="{{ $s }}" {{ (int)$from === (int)$s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="block text-xs text-gray-400 mb-1">To season</label>
          <select name="to" class="w-full bg-[#0f172a] border border-[#374151] rounded-lg px-3 py-2 focus:outline-none">
            @foreach($seasons as $s)
              <option value="{{ $s }}" {{ (int)$to === (int)$s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
          </select>
        </div>

        <div class="sm:col-span-2 lg:col-span-1">
          <label class="block text-xs text-gray-400 mb-1">Team (name or abbr.)</label>
          <input name="team" value="{{ $teamQuery }}"
                 placeholder="e.g. BOS or Celtics"
                 class="w-full bg-[#0f172a] border border-[#374151] rounded-lg px-3 py-2 focus:outline-none"
          />
        </div>

        <div class="flex gap-3">
          <button type="submit"
                  class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-[#84CC16] text-[#111827] font-semibold hover:bg-[#a3e635] transition w-full">
            Apply
          </button>
          <a href="{{ route('nba.standings.explorer') }}"
             class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-white/10 text-white hover:bg-white/20 transition w-full">
            Reset
          </a>
        </div>

        <div class="lg:col-span-4 flex flex-wrap items-center gap-3 pt-1">
          <input id="q" type="text" placeholder="Quick search in table…"
                 class="flex-1 min-w-[220px] bg-[#0f172a] border border-[#374151] rounded-lg px-3 py-2 focus:outline-none" />
          <a href="{{ route('nba.standings.explorer', array_merge(request()->query(), ['export' => 1])) }}"
             class="inline-flex items-center px-4 py-2 rounded-lg bg-white/10 text-white hover:bg-white/20 transition">
            Export CSV
          </a>
        </div>
      </form>
    </section>

    {{-- Compare selection bar --}}
    <section class="bg-[#1f2937] border border-[#374151] rounded-xl p-4 sm:p-5">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="text-sm text-gray-300">
          Select rows with the checkbox to compare (up to 5).
        </div>
        <div class="flex gap-2">
          <button id="compareBtn" class="px-3 py-2 rounded-lg bg-white/10 text-white hover:bg-white/20 disabled:opacity-40" disabled>
            Compare selected
          </button>
          <button id="clearSelBtn" class="px-3 py-2 rounded-lg bg-white/10 text-white hover:bg-white/20">
            Clear selection
          </button>
        </div>
      </div>

      {{-- Compare result (hidden until used) --}}
      <div id="compareArea" class="mt-4 hidden">
        <h3 class="text-white font-semibold mb-3">Comparison</h3>
        <div id="compareGrid" class="grid gap-4 [grid-template-columns:repeat(auto-fit,minmax(220px,1fr))]"></div>
      </div>
    </section>

    {{-- Table --}}
    <section class="bg-[#1f2937] border border-[#374151] rounded-xl overflow-hidden">
      <div class="overflow-x-auto">
        <table id="standingsTable" class="min-w-[1000px] w-full text-sm">
          <thead class="bg-[#0f172a] text-gray-300 sticky top-0 z-10">
            <tr>
              <th class="px-3 py-2 w-10"></th>
              <th data-sort="season" class="px-3 py-2 text-left cursor-pointer select-none hover:text-white">Season</th>
              <th data-sort="team_name" class="px-3 py-2 text-left cursor-pointer select-none hover:text-white">Team</th>
              <th data-sort="abbreviation" class="px-3 py-2 text-center cursor-pointer select-none hover:text-white">Abbr.</th>
              <th data-sort="wins" class="px-3 py-2 text-right cursor-pointer select-none hover:text-white">W</th>
              <th data-sort="losses" class="px-3 py-2 text-right cursor-pointer select-none hover:text-white">L</th>
              <th data-sort="win_percent" class="px-3 py-2 text-right cursor-pointer select-none hover:text-white">Win%</th>
              <th data-sort="playoff_seed" class="px-3 py-2 text-right cursor-pointer select-none hover:text-white">Seed</th>
              <th data-sort="games_behind" class="px-3 py-2 text-right cursor-pointer select-none hover:text-white">GB</th>
              <th data-sort="avg_points_for" class="px-3 py-2 text-right cursor-pointer select-none hover:text-white">PPG</th>
              <th data-sort="avg_points_against" class="px-3 py-2 text-right cursor-pointer select-none hover:text-white">OPP PPG</th>
              <th data-sort="point_differential" class="px-3 py-2 text-right cursor-pointer select-none hover:text-white">Diff</th>
              <th data-sort="home_record" class="px-3 py-2 text-center cursor-pointer select-none hover:text-white">Home</th>
              <th data-sort="road_record" class="px-3 py-2 text-center cursor-pointer select-none hover:text-white">Road</th>
              <th data-sort="last_ten" class="px-3 py-2 text-center cursor-pointer select-none hover:text-white">L10</th>
              <th data-sort="streak" class="px-3 py-2 text-center cursor-pointer select-none hover:text-white">Streak</th>
              <th data-sort="clincher" class="px-3 py-2 text-center cursor-pointer select-none hover:text-white">Clinch</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#374151] text-[#F3F4F6]">
            @foreach($rows as $r)
              <tr class="odd:bg-[#1f2937] even:bg-[#111827] hover:bg-[#374151]/60 transition"
                  data-season="{{ $r['season'] }}"
                  data-team="{{ $r['data_team'] }}">
                <td class="px-3 py-2 align-middle">
                  <input type="checkbox" class="rowSel accent-[#84CC16]"
                         data-payload='{{ $r['payload'] }}'>
                </td>
                <td class="px-3 py-2">{{ $r['season'] }}</td>

                <td class="px-3 py-2">
                  <a class="flex items-center gap-2 hover:text-[#84CC16]" href="{{ route('nba.team.show', $r['team_id']) }}">
                    @if(!empty($r['team_logo']))
                      <img src="{{ $r['team_logo'] }}" alt="{{ $r['team_name'] }} logo"
                          class="h-5 w-5 sm:h-6 sm:w-6 object-contain rounded bg-white p-[2px]" />
                    @else
                      <span class="inline-flex items-center justify-center h-5 w-5 sm:h-6 sm:w-6 rounded bg-white/10 text-[10px]">
                        {{ $r['abbreviation'] ?? '—' }}
                      </span>
                    @endif
                    <span class="truncate max-w-[180px] sm:max-w-[240px]">{{ $r['team_name'] }}</span>
                  </a>
                </td>

                <td class="px-3 py-2 text-center">{{ $r['abbreviation'] }}</td>
                <td class="px-3 py-2 text-right">{{ $r['wins'] }}</td>
                <td class="px-3 py-2 text-right">{{ $r['losses'] }}</td>
                <td class="px-3 py-2 text-right">{{ $r['win_percent_fmt'] }}</td>
                <td class="px-3 py-2 text-right">{{ $r['playoff_seed'] ?? '—' }}</td>
                <td class="px-3 py-2 text-right">{{ $r['games_behind'] ?? '—' }}</td>
                <td class="px-3 py-2 text-right">{{ $r['ppg_fmt'] }}</td>
                <td class="px-3 py-2 text-right">{{ $r['opp_ppg_fmt'] }}</td>
                <td class="px-3 py-2 text-right">
                  <span class="{{ $r['diff_class'] }}">{{ $r['diff_txt'] }}</span>
                </td>
                <td class="px-3 py-2 text-center">{{ $r['home_record'] ?? '—' }}</td>
                <td class="px-3 py-2 text-center">{{ $r['road_record'] ?? '—' }}</td>
                <td class="px-3 py-2 text-center">{{ $r['last_ten'] ?? '—' }}</td>
                <td class="px-3 py-2 text-center">{{ $r['streak_txt'] }}</td>
                <td class="px-3 py-2 text-center">{{ $r['clincher'] ?? '—' }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      {{-- Mobile hint --}}
      <p class="px-4 pb-4 pt-2 text-xs text-gray-400 sm:hidden">Tip: swipe sideways to see all columns.</p>
    </section>

    {{-- Legend --}}
    <section class="pb-8">
      <h2 class="text-xl sm:text-2xl font-semibold mb-3">Stat explanations</h2>
      <div class="grid gap-3 sm:gap-4 [grid-template-columns:repeat(auto-fit,minmax(180px,1fr))]">
        @foreach($legend as $item)
          <div class="bg-[#1f2937] border border-[#374151] rounded-xl p-3">
            <div class="text-sm font-semibold text-white mb-1">{{ $item[0] }}</div>
            <p class="text-xs text-gray-300">{{ $item[1] }}</p>
          </div>
        @endforeach
      </div>
    </section>

  </main>

<script>
  /* ===== Quick filter ===== */
  const q = document.getElementById('q');
  const rows = Array.from(document.querySelectorAll('#standingsTable tbody tr'));
  q?.addEventListener('input', (e) => {
    const term = e.target.value.trim().toLowerCase();
    rows.forEach(r => {
      const hay = (r.dataset.team + ' ' + r.dataset.season).toLowerCase();
      r.style.display = hay.includes(term) ? '' : 'none';
    });
  });

  /* ===== Sort (client-side) ===== */
  const headers = document.querySelectorAll('th[data-sort]');
  headers.forEach(h => {
    h.addEventListener('click', () => {
      const idx = Array.from(h.parentElement.children).indexOf(h);
      const tbody = document.querySelector('#standingsTable tbody');
      const trs = Array.from(tbody.querySelectorAll('tr')).filter(tr => tr.style.display !== 'none');

      const asc = !(h.dataset.asc === 'true');
      headers.forEach(x => x.removeAttribute('data-asc'));
      h.dataset.asc = asc;

      trs.sort((a,b) => {
        const A = a.children[idx].innerText.trim();
        const B = b.children[idx].innerText.trim();
        const nA = parseFloat(A.replace('+',''));
        const nB = parseFloat(B.replace('+',''));
        const bothNum = !isNaN(nA) && !isNaN(nB);
        if (bothNum) return asc ? (nA - nB) : (nB - nA);
        return asc ? A.localeCompare(B) : B.localeCompare(A);
      });

      tbody.append(...trs);
    });
  });

  /* ===== Compare selection ===== */
  const selBoxes   = document.querySelectorAll('.rowSel');
  const compareBtn = document.getElementById('compareBtn');
  const clearSelBtn= document.getElementById('clearSelBtn');
  const compareArea= document.getElementById('compareArea');
  const compareGrid= document.getElementById('compareGrid');

  function selectedPayloads() {
    return Array.from(selBoxes)
      .filter(x => x.checked)
      .slice(0,5)
      .map(x => JSON.parse(x.dataset.payload));
  }

  selBoxes.forEach(cb => {
    cb.addEventListener('change', () => {
      const sel = selectedPayloads();
      if (sel.length > 5) { cb.checked = false; return; }
      compareBtn.disabled = sel.length === 0;
    });
  });

  clearSelBtn.addEventListener('click', () => {
    selBoxes.forEach(x => x.checked = false);
    compareBtn.disabled = true;
    compareGrid.innerHTML = '';
    compareArea.classList.add('hidden');
  });

  /* ===== Helpers for leader-only comparison ===== */

  const num = (v) => (v === null || v === undefined || v === '—') ? NaN : Number(v);

  // For each stat, compute percent BEHIND the leader (0% = leader)
  function vsLeader(sel, field, higherIsBetter = true) {
    const values = sel.map(p => num(p[field]));
    const valid  = values.filter(v => isFinite(v));
    if (!valid.length) return sel.map(_ => ({ label: '—', cls: 'text-gray-300' }));

    const leader = higherIsBetter
      ? Math.max(...valid)   // best is max when higher is better
      : Math.min(...valid);  // best is min when lower is better

    return values.map(v => {
      if (!isFinite(v)) return { label: '—', cls: 'text-gray-300' };
      // percent behind leader (always >= 0 for non-leaders)
      let behindPct;
      if (leader === 0) {
        behindPct = 0; // avoid divide-by-zero; treat equals as leader
      } else if (higherIsBetter) {
        behindPct = ((leader - v) / Math.abs(leader)) * 100;
      } else {
        behindPct = ((v - leader) / Math.abs(leader)) * 100;
      }
      if (Math.abs(behindPct) < 0.5) {
        // close enough to leader → mark as leader
        return { label: 'Leader', cls: 'text-[#84CC16]' };
      }
      const label = `-${Math.round(behindPct)}% vs leader`;
      return { label, cls: 'text-[#F97316]' };
    });
  }

  function lineLeader(comp) {
    return `<div class="text-xs mt-0.5 ${comp.cls}">${comp.label}</div>`;
  }

  /* ===== Compare click ===== */
  compareBtn.addEventListener('click', () => {
    const sel = selectedPayloads();
    if (!sel.length) { compareArea.classList.add('hidden'); return; }

    // Which direction is "better":
    const cmpWin  = vsLeader(sel, 'win_percent', true);
    const cmpPPG  = vsLeader(sel, 'ppg',         true);
    const cmpOPP  = vsLeader(sel, 'opp_ppg',     false); // lower is better
    const cmpDiff = vsLeader(sel, 'diff',        true);

    compareGrid.innerHTML = sel.map((p, idx) => {
      const winPct   = (p.win_percent ?? null) !== null ? `${Math.round(Number(p.win_percent) * 100)}%` : '—';
      const diffTxt  = (p.diff ?? null) === null ? '—' : (p.diff >= 0 ? ('+'+p.diff) : p.diff);
      const streakTxt= (p.streak ?? null) === null ? '—' : (p.streak > 0 ? 'W'+p.streak : (p.streak < 0 ? 'L'+Math.abs(p.streak) : '—'));
      const logoImg  = p.logo
        ? `<img src="${p.logo}" alt="${p.team} logo" class="h-6 w-6 object-contain rounded bg-white p-[2px]" />`
        : `<span class="inline-flex items-center justify-center h-6 w-6 rounded bg-white/10 text-[10px]">${p.abbr ?? '—'}</span>`;

      return `
        <article class="bg-[#0f172a]/60 border border-[#374151] rounded-xl p-4">
          <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-2">
              ${logoImg}
              <div class="text-white font-semibold">${p.team} (${p.abbr ?? '—'})</div>
            </div>
            <div class="text-xs text-[#F3F4F6]/70">${p.season}</div>
          </div>

          <div class="grid grid-cols-3 gap-3 text-sm">
            <div>
              <div class="text-[#F3F4F6]/60 text-xs">W/L</div>
              <div class="font-semibold">${p.wins ?? '—'}–${p.losses ?? '—'}</div>
              ${lineLeader(cmpWin[idx])}
            </div>

            <div>
              <div class="text-[#F3F4F6]/60 text-xs">Win%</div>
              <div class="font-semibold">${winPct}</div>
              ${lineLeader(cmpWin[idx])}
            </div>

            <div>
              <div class="text-[#F3F4F6]/60 text-xs">Seed</div>
              <div class="font-semibold">${p.seed ?? '—'}</div>
            </div>

            <div>
              <div class="text-[#F3F4F6]/60 text-xs">PPG</div>
              <div class="font-semibold">${p.ppg ?? '—'}</div>
              ${lineLeader(cmpPPG[idx])}
            </div>

            <div>
              <div class="text-[#F3F4F6]/60 text-xs">OPP PPG</div>
              <div class="font-semibold">${p.opp_ppg ?? '—'}</div>
              ${lineLeader(cmpOPP[idx])}
            </div>

            <div>
              <div class="text-[#F3F4F6]/60 text-xs">Diff</div>
              <div class="font-semibold ${p.diff==null?'':(p.diff>=0?'text-[#84CC16]':'text-[#F97316]')}">${diffTxt}</div>
              ${lineLeader(cmpDiff[idx])}
            </div>
          </div>

          <div class="mt-3 flex flex-wrap gap-2 text-xs">
            <span class="px-2.5 py-1 rounded-full bg-white/5 border border-white/10">Home: ${p.home ?? '—'}</span>
            <span class="px-2.5 py-1 rounded-full bg-white/5 border border-white/10">Road: ${p.road ?? '—'}</span>
            <span class="px-2.5 py-1 rounded-full bg-white/5 border border-white/10">L10: ${p.l10 ?? '—'}</span>
            <span class="px-2.5 py-1 rounded-full bg-white/5 border border-white/10">Streak: ${streakTxt}</span>
            ${p.clincher ? `<span class="px-2.5 py-1 rounded-full bg-white/5 border border-white/10">Clincher: ${p.clincher}</span>` : ''}
          </div>
        </article>
      `;
    }).join('');

    compareArea.classList.toggle('hidden', sel.length === 0);
  });
</script>



</body>
</html>
@endsection