@extends('layouts.app')
@section('title','LBS — Spēlētāju salīdzinātājs')

@section('subnav')
  @isset ($subLeague)
    <x-lbs-subnav :subLeague="$subLeague" />
  @endisset
@endsection

@section('content')
<main class="max-w-7xl mx-auto px-4 pb-16 pt-24 space-y-8">

  {{-- Filters --}}
  <section class="bg-[#1f2937] border border-[#374151] rounded-2xl p-4 sm:p-5">
    <form method="GET" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-8 items-end">
      <div class="lg:col-span-2">
        <label class="block text-xs text-gray-400 mb-1">No sezonas</label>
        <select name="from" class="w-full bg-[#0f172a] border border-[#374151] rounded-lg px-3 py-2">
          @foreach($seasons as $s)
            <option value="{{ $s }}" @selected((int)$from === (int)$s)>{{ $s }}</option>
          @endforeach
        </select>
      </div>

      <div class="lg:col-span-2">
        <label class="block text-xs text-gray-400 mb-1">Līdz sezonai</label>
        <select name="to" class="w-full bg-[#0f172a] border border-[#374151] rounded-lg px-3 py-2">
          @foreach($seasons as $s)
            <option value="{{ $s }}" @selected((int)$to === (int)$s)>{{ $s }}</option>
          @endforeach
        </select>
      </div>

      <div class="lg:col-span-2">
        <label class="block text-xs text-gray-400 mb-1">Vecāklīga</label>
        <select id="parentSelect" name="league" class="w-full bg-[#0f172a] border border-[#374151] rounded-lg px-3 py-2">
          <option value="">— Visas —</option>
          @foreach($parents as $p)
            <option value="{{ $p->id }}" @selected(!empty($selectedParent) && (int)$selectedParent === (int)$p->id)>
              {{ $p->name }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="lg:col-span-2">
        <label class="block text-xs text-gray-400 mb-1">Apakšlīga</label>
        <select id="subSelect" name="sub" class="w-full bg-[#0f172a] border border-[#374151] rounded-lg px-3 py-2">
          <option value="">— Visas —</option>
          @foreach($subs as $s)
            <option value="{{ $s->id }}" data-parent="{{ $s->parent_id }}"
              @selected(!empty($selectedSub) && (int)$selectedSub === (int)$s->id)>
              {{ $s->name }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="lg:col-span-8 flex flex-wrap items-center gap-3 pt-1">
        <input id="q" type="text" placeholder="Ātrā meklēšana tabulā…"
               class="flex-1 min-w-[220px] bg-[#0f172a] border border-[#374151] rounded-lg px-3 py-2" />
      </div>
    </form>
  </section>

  {{-- Compare selection bar --}}
  <section class="bg-[#1f2937] border border-[#374151] rounded-2xl p-4 sm:p-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <div class="text-sm text-gray-300">Atzīmē rindas (līdz 5) un salīdzini.</div>
      <div class="flex gap-2">
        <button id="compareBtn" class="px-3 py-2 rounded-lg bg-white/10 text-white hover:bg-white/20 disabled:opacity-40" disabled>
          Salīdzināt izvēlētos
        </button>
        <button id="clearSelBtn" class="px-3 py-2 rounded-lg bg-white/10 text-white hover:bg-white/20">
          Notīrīt
        </button>
      </div>
    </div>

    <div id="compareArea" class="mt-4 hidden">
      <h3 class="text-white font-semibold mb-3">Salīdzinājums</h3>
      <div id="compareGrid" class="grid gap-4 [grid-template-columns:repeat(auto-fit,minmax(240px,1fr))]"></div>
    </div>
  </section>

  {{-- Table --}}
  <section class="bg-[#1f2937] border border-[#374151] rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
      <table id="playersTable" class="min-w-[1100px] w-full text-sm">
        <thead class="bg-[#0f172a] text-gray-300 sticky top-0 z-10">
          <tr>
            <th class="px-3 py-2 w-10"></th>
            <th data-sort="season" class="px-3 py-2 cursor-pointer select-none hover:text-white">Sezona</th>
            <th class="px-3 py-2 text-left">Spēlētājs</th>
            <th class="px-3 py-2 text-left">Komanda</th>
            <th data-sort="games" class="px-3 py-2 text-right cursor-pointer select-none hover:text-white">G</th>
            <th class="px-3 py-2 text-right">W/L</th>
            <th data-sort="ppg" class="px-3 py-2 text-right cursor-pointer select-none hover:text-white">PPG</th>
            <th data-sort="rpg" class="px-3 py-2 text-right cursor-pointer select-none hover:text-white">RPG</th>
            <th data-sort="apg" class="px-3 py-2 text-right cursor-pointer select-none hover:text-white">APG</th>
            <th data-sort="spg" class="px-3 py-2 text-right cursor-pointer select-none hover:text-white">SPG</th>
            <th data-sort="bpg" class="px-3 py-2 text-right cursor-pointer select-none hover:text-white">BPG</th>
            <th data-sort="tpg" class="px-3 py-2 text-right cursor-pointer select-none hover:text-white">TOV</th>
            <th data-sort="fg_pct" class="px-3 py-2 text-right cursor-pointer select-none hover:text-white">FG%</th>
            <th data-sort="tp_pct" class="px-3 py-2 text-right cursor-pointer select-none hover:text-white">3P%</th>
            <th data-sort="ft_pct" class="px-3 py-2 text-right cursor-pointer select-none hover:text-white">FT%</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#374151] text-[#F3F4F6]">
          @foreach($rows as $r)
            <tr class="odd:bg-[#1f2937] even:bg-[#111827] hover:bg-[#374151]/60 transition"
                data-season="{{ $r['season'] }}"
                data-player="{{ $r['data_player'] }}"
                data-parent="{{ $r['parent_league_id'] ?? '' }}"
                data-sub="{{ $r['subleague_id'] ?? '' }}">
              <td class="px-3 py-2">
                <input type="checkbox" class="rowSel accent-[#84CC16]" data-payload='{{ $r['payload'] }}'>
              </td>
              <td class="px-3 py-2">{{ $r['season'] }}</td>
              <td class="px-3 py-2">
                <div class="flex items-center gap-2">
                  @php $head = $r['player_photo']; @endphp
                  @if(!empty($head))
                    <img src="{{ \Illuminate\Support\Str::startsWith($head, ['http://','https://']) ? $head : asset('storage/'.$head) }}"
                         class="h-6 w-6 rounded-full object-cover ring-1 ring-white/10" alt="">
                  @else
                    <div class="h-6 w-6 rounded-full bg-white/10"></div>
                  @endif
                  <span class="hover:text-[#84CC16]">{{ $r['player'] }}</span>
                </div>
              </td>
              <td class="px-3 py-2">
                <div class="flex items-center gap-2">
                  @if(!empty($r['team_logo']))
                    <img src="{{ asset('storage/' . $r['team_logo']) }}"
                         class="h-5 w-5 object-contain rounded bg-white p-[2px]" alt="">
                  @endif
                  <a class="hover:text-[#84CC16]" href="{{ route('lbs.team.show', $r['team_id']) }}">{{ $r['team'] }}</a>
                </div>
              </td>
              <td class="px-3 py-2 text-right">{{ $r['games'] }}</td>
              <td class="px-3 py-2 text-right">{{ $r['wl_text'] }}</td>
              <td class="px-3 py-2 text-right">{{ $r['ppg'] }}</td>
              <td class="px-3 py-2 text-right">{{ $r['rpg'] }}</td>
              <td class="px-3 py-2 text-right">{{ $r['apg'] }}</td>
              <td class="px-3 py-2 text-right">{{ $r['spg'] }}</td>
              <td class="px-3 py-2 text-right">{{ $r['bpg'] }}</td>
              <td class="px-3 py-2 text-right">{{ $r['tpg'] }}</td>
              <td class="px-3 py-2 text-right">{{ $r['fg_pct'] }}</td>
              <td class="px-3 py-2 text-right">{{ $r['tp_pct'] }}</td>
              <td class="px-3 py-2 text-right">{{ $r['ft_pct'] }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <p class="px-4 pb-4 pt-2 text-xs text-gray-400 sm:hidden">Padoms: pavelc horizontāli, lai redzētu visas kolonnas.</p>
  </section>

  {{-- Legend --}}
  <section class="pb-8">
    <h2 class="text-xl sm:text-2xl font-semibold mb-3">Statistikas skaidrojumi</h2>
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
  const STORAGE_BASE = @json(asset('storage'));

  // Dependent subleague dropdown
  (function () {
    const parentSel = document.getElementById('parentSelect');
    const subSel    = document.getElementById('subSelect');
    if (!parentSel || !subSel) return;

    function syncSubOptions() {
      const pid = parentSel.value;
      Array.from(subSel.options).forEach(opt => {
        if (!opt.value) { opt.hidden = false; return; } // "Visas"
        const p = opt.getAttribute('data-parent');
        opt.hidden = !!pid && p !== pid;
      });
      const current = subSel.options[subSel.selectedIndex];
      if (current && current.hidden) subSel.value = '';
    }

    parentSel.addEventListener('change', () => { syncSubOptions(); applyFilters(); });
    subSel.addEventListener('change', applyFilters);
    syncSubOptions();
  })();

  const q         = document.getElementById('q');
  const rows      = Array.from(document.querySelectorAll('#playersTable tbody tr'));
  const parentSel = document.getElementById('parentSelect');
  const subSel    = document.getElementById('subSelect');

  function applyFilters() {
    const term = (q?.value || '').trim().toLowerCase();
    const pSel = parentSel?.value || '';
    const sSel = subSel?.value || '';

    rows.forEach(r => {
      const hay = (r.dataset.player + ' ' + r.dataset.season).toLowerCase();
      const okQ = hay.includes(term);

      const rp = r.getAttribute('data-parent') || '';
      const rs = r.getAttribute('data-sub') || '';

      const okP = !pSel || rp === pSel;
      const okS = !sSel || rs === sSel;

      r.style.display = (okQ && okP && okS) ? '' : 'none';
    });
  }

  q?.addEventListener('input', applyFilters);
  document.addEventListener('DOMContentLoaded', applyFilters);

  // Sort (client-side) on visible rows
  const headers = document.querySelectorAll('#playersTable thead th[data-sort]');
  headers.forEach(h => {
    h.addEventListener('click', () => {
      const tbody = document.querySelector('#playersTable tbody');
      const idx   = Array.from(h.parentElement.children).indexOf(h);
      const asc   = !(h.dataset.asc === 'true');
      headers.forEach(x => x.removeAttribute('data-asc'));
      h.dataset.asc = asc;

      const visible = Array.from(tbody.querySelectorAll('tr'))
        .filter(tr => tr.style.display !== 'none');

      const parseNum = (text) => {
        if (!text) return NaN;
        const t = text.replaceAll(',', '').replace('%','').replace('—','').trim();
        const n = parseFloat(t);
        return isNaN(n) ? NaN : n;
      };

      visible.sort((a,b) => {
        const A = a.children[idx].innerText.trim();
        const B = b.children[idx].innerText.trim();
        const An = parseNum(A), Bn = parseNum(B);
        const both = isFinite(An) && isFinite(Bn);
        if (both) return asc ? (An - Bn) : (Bn - An);
        return asc ? A.localeCompare(B) : B.localeCompare(A);
      });

      tbody.append(...visible);
    });
  });

  // Compare selection + cards
  const selBoxes    = document.querySelectorAll('.rowSel');
  const compareBtn  = document.getElementById('compareBtn');
  const clearSelBtn = document.getElementById('clearSelBtn');
  const compareArea = document.getElementById('compareArea');
  const compareGrid = document.getElementById('compareGrid');

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

  const num = (v) => (v===null||v===undefined||v==='—') ? NaN : Number(v);
  function vsLeader(sel, field, higherIsBetter=true) {
    const values = sel.map(p => num(p[field]));
    const valid  = values.filter(v => isFinite(v));
    if (!valid.length) return sel.map(_ => ({label:'—', cls:'text-gray-300'}));
    const leader = higherIsBetter ? Math.max(...valid) : Math.min(...valid);
    return values.map(v => {
      if (!isFinite(v)) return {label:'—', cls:'text-gray-300'};
      let behindPct;
      if (leader === 0) behindPct = 0;
      else if (higherIsBetter) behindPct = ((leader - v) / Math.abs(leader)) * 100;
      else behindPct = ((v - leader) / Math.abs(leader)) * 100;
      if (Math.abs(behindPct) < 0.5) return {label:'Līderis', cls:'text-[#84CC16]'};
      return {label:`-${Math.round(behindPct)}% sal. ar līderi`, cls:'text-[#F97316]'};
    });
  }
  const line = (c) => `<div class="text-xs mt-0.5 ${c.cls}">${c.label}</div>`;

  // Build absolute/relative image URLs for cards
  const toUrl = (v) => {
    if (!v) return null;
    const s = String(v);
    if (/^https?:\/\//i.test(s)) return s;
    return `${STORAGE_BASE}/${s.replace(/^\/+/, '')}`;
  };

  compareBtn.addEventListener('click', () => {
    const sel = selectedPayloads();
    if (!sel.length) { compareArea.classList.add('hidden'); return; }

    const cmpPPG = vsLeader(sel, 'ppg', true);
    const cmpRPG = vsLeader(sel, 'rpg', true);
    const cmpAPG = vsLeader(sel, 'apg', true);
    const cmpSPG = vsLeader(sel, 'spg', true);
    const cmpBPG = vsLeader(sel, 'bpg', true);
    const cmpTOV = vsLeader(sel, 'tpg', false);
    const cmpFG  = vsLeader(sel, 'fg_pct', true);
    const cmpTP  = vsLeader(sel, 'tp_pct', true);
    const cmpFT  = vsLeader(sel, 'ft_pct', true);

    compareGrid.innerHTML = sel.map((p,i) => {
      const head = p.headshot ? `<img src="${toUrl(p.headshot)}" class="h-7 w-7 rounded-full object-cover ring-1 ring-white/10" />`
                              : `<div class="h-7 w-7 rounded-full bg-white/10"></div>`;
      const logo = p.logo ? `<img src="${toUrl(p.logo)}" class="h-6 w-6 object-contain rounded bg-white p-[2px]" />` : '';

      const pct = (v) => (v==null ? '—' : `${(Number(v)*100).toFixed(1)}%`);
      const one = (v) => (v==null ? '—' : Number(v).toFixed(1));

      return `
        <article class="bg-[#0f172a]/60 border border-[#374151] rounded-xl p-4">
          <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-2">
              ${head}
              <div class="text-white font-semibold">${p.player}</div>
            </div>
            <div class="flex items-center gap-2">${logo}<span class="text-xs text-gray-300">${p.team ?? ''}</span></div>
          </div>
          <div class="grid grid-cols-3 gap-3 text-sm">
            <div><div class="text-[#F3F4F6]/60 text-xs">PPG</div><div class="font-semibold">${one(p.ppg)}</div>${line(cmpPPG[i])}</div>
            <div><div class="text-[#F3F4F6]/60 text-xs">RPG</div><div class="font-semibold">${one(p.rpg)}</div>${line(cmpRPG[i])}</div>
            <div><div class="text-[#F3F4F6]/60 text-xs">APG</div><div class="font-semibold">${one(p.apg)}</div>${line(cmpAPG[i])}</div>
            <div><div class="text-[#F3F4F6]/60 text-xs">SPG</div><div class="font-semibold">${one(p.spg)}</div>${line(cmpSPG[i])}</div>
            <div><div class="text-[#F3F4F6]/60 text-xs">BPG</div><div class="font-semibold">${one(p.bpg)}</div>${line(cmpBPG[i])}</div>
            <div><div class="text-[#F3F4F6]/60 text-xs">TOV</div><div class="font-semibold">${one(p.tpg)}</div>${line(cmpTOV[i])}</div>
            <div><div class="text-[#F3F4F6]/60 text-xs">FG%</div><div class="font-semibold">${pct(p.fg_pct)}</div>${line(cmpFG[i])}</div>
            <div><div class="text-[#F3F4F6]/60 text-xs">3P%</div><div class="font-semibold">${pct(p.tp_pct)}</div>${line(cmpTP[i])}</div>
            <div><div class="text-[#F3F4F6]/60 text-xs">FT%</div><div class="font-semibold">${pct(p.ft_pct)}</div>${line(cmpFT[i])}</div>
          </div>
          <div class="mt-3 text-xs text-gray-300">G: ${p.games ?? '—'} • W/L: ${p.wins ?? '—'}–${p.losses ?? '—'}</div>
        </article>
      `;
    }).join('');

    compareArea.classList.remove('hidden');
  });
</script>
@endsection
