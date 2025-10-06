@extends('layouts.app')
@section('title', $team->name . ' — Spēles')

@section('subnav')
  <x-teamnav :team="$team" />
@endsection

@section('content')
  <div class="max-w-6xl mx-auto px-4 space-y-12 pt-6">

    <section>
      <h2 class="text-2xl font-bold text-white mb-4">Komandas rezultāts</h2>
      @php
        $wins   = $games->where('winner_id', $team->id)->count();
        $losses = $games->count() - $wins;
      @endphp
      <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-4">
        <button id="filterWins"
                type="button"
                class="min-h-[96px] p-4 bg-[#1f2937] border border-[#374151] rounded-xl text-center shadow hover:border-[#84CC16] transition focus:outline-none focus:ring-2 focus:ring-[#84CC16]/40">
          <p class="text-2xl font-extrabold text-[#84CC16] tabular-nums">{{ $wins }}</p>
          <p class="text-xs text-[#F3F4F6]/70 mt-1">Uzvaras</p>
        </button>

        <button id="filterLosses"
                type="button"
                class="min-h-[96px] p-4 bg-[#1f2937] border border-[#374151] rounded-xl text-center shadow hover:border-[#F97316] transition focus:outline-none focus:ring-2 focus:ring-[#F97316]/40">
          <p class="text-2xl font-extrabold text-[#F97316] tabular-nums">{{ $losses }}</p>
          <p class="text-xs text-[#F3F4F6]/70 mt-1">Zaudējumi</p>
        </button>

        <button id="filterAll"
                type="button"
                class="hidden sm:block min-h-[96px] p-4 bg-[#1f2937] border border-[#374151] rounded-xl text-center shadow hover:border-white/40 transition focus:outline-none focus:ring-2 focus:ring-white/30">
          <p class="text-2xl font-extrabold text-white tabular-nums">Visas</p>
          <p class="text-xs text-[#F3F4F6]/70 mt-1">Rādīt visas spēles</p>
        </button>
      </div>
    </section>

    <section>
      <h2 class="text-2xl font-bold text-white mb-4">Spēles</h2>

      @if($games->isEmpty())
        <p class="mt-2 text-[#F3F4F6]/70">Šai komandai vēl nav spēļu.</p>
      @else
        <div class="space-y-10">

          @if($upcomingGames->isNotEmpty())
            <div>
              <h3 class="text-xl font-semibold text-[#84CC16] mb-4">Gaidāmās spēles</h3>
              <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($upcomingGames as $game)
                  <article class="group bg-[#1f2937] border border-[#374151] rounded-2xl shadow hover:shadow-xl transition p-5 flex flex-col items-center text-center">
                    <span class="mb-3 inline-block px-3 py-1 text-[10px] font-bold uppercase rounded-full bg-[#F97316] text-white tracking-wide">
                      Gaidāmā spēle
                    </span>

                    <div class="flex-1 flex items-center justify-center gap-6">
                      <div class="flex flex-col items-center gap-2 w-28">
                        <div class="h-16 w-16 bg-[#111827] rounded-xl grid place-items-center overflow-hidden">
                          @if($game->team1?->logo)
                            <img src="{{ asset('storage/' . $game->team1->logo) }}"
                                 alt="{{ $game->team1->name }}"
                                 class="h-full w-full object-contain"
                                 loading="lazy">
                          @endif
                        </div>
                        <span class="text-sm font-semibold">{{ $game->team1->name }}</span>
                      </div>

                      <div class="text-[#F3F4F6]/60 font-semibold">vs</div>

                      <div class="flex flex-col items-center gap-2 w-28">
                        <div class="h-16 w-16 bg-[#111827] rounded-xl grid place-items-center overflow-hidden">
                          @if($game->team2?->logo)
                            <img src="{{ asset('storage/' . $game->team2->logo) }}"
                                 alt="{{ $game->team2->name }}"
                                 class="h-full w-full object-contain"
                                 loading="lazy">
                          @endif
                        </div>
                        <span class="text-sm font-semibold">{{ $game->team2->name }}</span>
                      </div>
                    </div>

                    <div class="mt-4 text-sm text-[#F3F4F6]/70">
                      🗓 {{ $game->date ? \Carbon\Carbon::parse($game->date)->format('d.m.Y H:i') : '—' }}
                    </div>

                    <a href="{{ route('lbs.game.detail', $game->id) }}"
                       class="mt-4 inline-flex items-center justify-center w-full px-4 py-2 rounded-lg bg-[#84CC16] text-[#111827] font-semibold hover:bg-[#a3e635] transition">
                      Skatīt detaļas
                    </a>
                  </article>
                @endforeach
              </div>
            </div>
          @endif

          @if($pastGames->isNotEmpty())
            <div>
              <div class="flex items-center justify-between">
                <h3 class="text-xl font-semibold text-white mb-4">Aizvadītās spēles</h3>
                <div id="activeFilterBadge" class="hidden mb-4 px-2.5 py-1 rounded-full text-xs bg-white/5 border border-white/10 text-gray-300">
                  Filtrs: <span class="font-semibold ml-1" data-label>—</span>
                </div>
              </div>

              <div id="pastGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($pastGames as $game)
                  <article
                    class="group bg-[#1f2937] border border-[#374151] rounded-2xl shadow hover:shadow-xl transition p-5 flex flex-col items-center text-center"
                    data-game-card="past"
                    data-result="{{ $game->is_win ? 'win' : ($game->is_loss ? 'loss' : 'na') }}"
                  >
                    <div class="flex-1 flex items-center justify-center gap-6">
                      <div class="flex flex-col items-center gap-2 w-28">
                        <div class="h-16 w-16 bg-[#111827] rounded-xl grid place-items-center overflow-hidden">
                          @if($game->team1?->logo)
                            <img src="{{ asset('storage/' . $game->team1->logo) }}"
                                 alt="{{ $game->team1->name }}"
                                 class="h-full w-full object-contain"
                                 loading="lazy">
                          @endif
                        </div>
                        <span class="text-sm font-semibold">{{ $game->team1->name }}</span>
                      </div>

                      <div class="text-center">
                        <div class="text-1xl font-extrabold text-white tabular-nums">
                          {{ $game->score1 }} : {{ $game->score2 }}
                        </div>
                        <div class="text-xs text-[#F3F4F6]/60 mt-1">
                          {{ $game->is_win ? 'Uzvara' : ($game->is_loss ? 'Zaudējums' : 'Rezultāts') }}
                        </div>
                      </div>

                      <div class="flex flex-col items-center gap-2 w-28">
                        <div class="h-16 w-16 bg-[#111827] rounded-xl grid place-items-center overflow-hidden">
                          @if($game->team2?->logo)
                            <img src="{{ asset('storage/' . $game->team2->logo) }}"
                                 alt="{{ $game->team2->name }}"
                                 class="h-full w-full object-contain"
                                 loading="lazy">
                          @endif
                        </div>
                        <span class="text-sm font-semibold">{{ $game->team2->name }}</span>
                      </div>
                    </div>

                    <div class="mt-4 text-sm text-[#F3F4F6]/70">
                      🗓 {{ $game->date ? \Carbon\Carbon::parse($game->date)->format('d.m.Y H:i') : '—' }}
                    </div>

                    <a href="{{ route('lbs.game.detail', $game->id) }}"
                       class="mt-4 inline-flex items-center justify-center w-full px-4 py-2 rounded-lg bg-white/10 text-white font-semibold hover:bg-white/20 transition">
                      Skatīt detalizētu statistiku
                    </a>
                  </article>
                @endforeach
              </div>
            </div>
          @endif

        </div>
      @endif
    </section>

  </div>

  <script>
    (function () {
      const winsBtn   = document.getElementById('filterWins');
      const lossesBtn = document.getElementById('filterLosses');
      const allBtn    = document.getElementById('filterAll');
      const cards     = Array.from(document.querySelectorAll('[data-game-card="past"]'));
      const badge     = document.getElementById('activeFilterBadge');
      const label     = badge?.querySelector('[data-label]');

      function setBadge(txt) {
        if (!badge || !label) return;
        if (!txt) { badge.classList.add('hidden'); return; }
        label.textContent = txt;
        badge.classList.remove('hidden');
      }

      function clearActive() {
        [winsBtn, lossesBtn, allBtn].forEach(b => b?.classList.remove('ring-2', 'ring-offset-1', 'ring-[#84CC16]'));
      }

      function filter(type) {
        cards.forEach(c => {
          const r = c.getAttribute('data-result'); 
          let show = true;
          if (type === 'win')  show = (r === 'win');
          if (type === 'loss') show = (r === 'loss');
          c.style.display = show ? '' : 'none';
        });
      }

      winsBtn?.addEventListener('click', () => {
        filter('win');
        clearActive();
        winsBtn.classList.add('ring-2', 'ring-offset-1', 'ring-[#84CC16]');
        setBadge('Tikai uzvaras');
      });

      lossesBtn?.addEventListener('click', () => {
        filter('loss');
        clearActive();
        lossesBtn.classList.add('ring-2', 'ring-offset-1', 'ring-[#84CC16]');
        setBadge('Tikai zaudējumi');
      });

      allBtn?.addEventListener('click', () => {
        filter('all');
        clearActive();
        allBtn.classList.add('ring-2', 'ring-offset-1', 'ring-[#84CC16]');
        setBadge('');
      });
    })();
  </script>
@endsection
