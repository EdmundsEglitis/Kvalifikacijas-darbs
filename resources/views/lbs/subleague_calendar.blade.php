<!DOCTYPE html>
<html lang="lv" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $subLeague->name }} – Kalendārs</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="antialiased bg-[#111827] text-[#F3F4F6]">

  {{-- Main + Sub tabs (styled + scroll-reactive) --}}
  <x-sub-league-tabs :parentLeagues="$parentLeagues" :subLeague="$subLeague" />

  <main class="pt-32 max-w-6xl mx-auto px-4">
    <header class="mb-8">
      <h1 class="text-3xl sm:text-4xl font-extrabold text-white">{{ $subLeague->name }} — Kalendārs</h1>
      @if(!empty($seasonLabel))
        <p class="mt-2 text-[#F3F4F6]/70">{{ $seasonLabel }}</p>
      @endif
    </header>

    @if($games->isEmpty())
      <p class="mt-6 text-[#F3F4F6]/70">Nav pieejamu spēļu.</p>
    @else
      {{-- UPCOMING --}}
      @if($upcomingGames->isNotEmpty())
        <section class="mb-12">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-bold text-white">Gaidāmās spēles</h2>
            <span class="text-xs px-2 py-1 rounded-full bg-[#84CC16]/20 text-[#84CC16]">
              {{ $upcomingGames->count() }}
            </span>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($upcomingGames as $game)
              <article class="group bg-[#1f2937] border border-[#374151] rounded-2xl shadow transition hover:-translate-y-0.5 hover:shadow-xl min-h-[260px] p-5 flex flex-col">
                <div class="flex justify-center mb-3">
                  <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-[#F97316] text-white tracking-wide">GAIDĀMĀ SPĒLE</span>
                </div>

                <div class="flex-1 flex items-center justify-center gap-6">
                  {{-- Team 1 --}}
                  <div class="flex flex-col items-center gap-2 w-28 text-center">
                    <div class="h-16 w-16 rounded-xl bg-[#111827] grid place-items-center overflow-hidden">
                      @if(optional($game->team1)->logo)
                        <img src="{{ asset('storage/'. $game->team1->logo) }}" alt="{{ $game->team1->name }}" class="h-full w-full object-contain" loading="lazy">
                      @else
                        <span class="text-xs text-gray-500">No Logo</span>
                      @endif
                    </div>
                    <a href="{{ route('lbs.team.overview', $game->team1->id) }}"
                       class="text-sm font-semibold hover:text-[#84CC16] transition">
                      {{ $game->team1->name }}
                    </a>
                  </div>

                  <div class="text-[#F3F4F6]/60 font-semibold">vs</div>

                  {{-- Team 2 --}}
                  <div class="flex flex-col items-center gap-2 w-28 text-center">
                    <div class="h-16 w-16 rounded-xl bg-[#111827] grid place-items-center overflow-hidden">
                      @if(optional($game->team2)->logo)
                        <img src="{{ asset('storage/'. $game->team2->logo) }}" alt="{{ $game->team2->name }}" class="h-full w-full object-contain" loading="lazy">
                      @else
                        <span class="text-xs text-gray-500">No Logo</span>
                      @endif
                    </div>
                    <a href="{{ route('lbs.team.overview', $game->team2->id) }}"
                       class="text-sm font-semibold hover:text-[#84CC16] transition">
                      {{ $game->team2->name }}
                    </a>
                  </div>
                </div>

                <div class="mt-4 text-center text-sm text-[#F3F4F6]/80">
                  🗓 {{ optional($game->date)->format('d.m.Y H:i') }}
                  @if(!empty($game->venue))
                    · 📍 {{ $game->venue }}
                  @endif
                </div>

                <div class="mt-4">
                  <a href="{{ route('lbs.game.detail', $game->id) }}"
                     class="inline-flex items-center justify-center w-full px-4 py-2 rounded-lg bg-[#84CC16] text-[#111827] font-semibold hover:bg-[#a3e635] transition">
                    Skatīt detaļas
                  </a>
                </div>
              </article>
            @endforeach
          </div>
        </section>
      @endif

      {{-- COMPLETED --}}
      @if($pastGames->isNotEmpty())
        <section>
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-bold text-white">Aizvadītās spēles</h2>
            <span class="text-xs px-2 py-1 rounded-full bg-white/10 text-white">
              {{ $pastGames->count() }}
            </span>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($pastGames as $game)
              @php
                // Prefer accessor $game->final_score (score string OR computed from quarters)
                $final = $game->final_score ?? null;
                $s1 = $s2 = null;
                if ($final && str_contains($final, '-')) {
                    [$s1, $s2] = array_map('trim', explode('-', $final, 2));
                }
                $isWinner1 = $game->winner_id && $game->winner_id === $game->team1_id;
                $isWinner2 = $game->winner_id && $game->winner_id === $game->team2_id;
              @endphp

              <article class="group bg-[#1f2937] border border-[#374151] rounded-2xl shadow transition hover:-translate-y-0.5 hover:shadow-xl min-h-[260px] p-5 flex flex-col">
                <div class="flex-1 flex items-center justify-center gap-6">
                  {{-- Team 1 --}}
                  <div class="flex flex-col items-center gap-2 w-28 text-center">
                    <div class="h-16 w-16 rounded-xl bg-[#111827] grid place-items-center overflow-hidden {{ $isWinner1 ? 'ring-2 ring-[#84CC16]' : '' }}">
                      @if(optional($game->team1)->logo)
                        <img src="{{ asset('storage/'. $game->team1->logo) }}" alt="{{ $game->team1->name }}" class="h-full w-full object-contain" loading="lazy">
                      @else
                        <span class="text-xs text-gray-500">No Logo</span>
                      @endif
                    </div>
                    <a href="{{ route('lbs.team.overview', $game->team1->id) }}"
                       class="text-sm font-semibold transition {{ $isWinner1 ? 'text-[#84CC16]' : 'hover:text-[#84CC16]' }}">
                      {{ $game->team1->name }}
                    </a>
                  </div>

                  {{-- Score --}}
                  <div class="text-center">
                    <div class="text-3xl font-extrabold text-white tracking-wide">
                      {{ ($s1 !== null && $s2 !== null) ? ($s1.' : '.$s2) : '—' }}
                    </div>
                    <div class="text-xs mt-1 text-[#F3F4F6]/60">Galarezultāts</div>
                  </div>

                  {{-- Team 2 --}}
                  <div class="flex flex-col items-center gap-2 w-28 text-center">
                    <div class="h-16 w-16 rounded-xl bg-[#111827] grid place-items-center overflow-hidden {{ $isWinner2 ? 'ring-2 ring-[#84CC16]' : '' }}">
                      @if(optional($game->team2)->logo)
                        <img src="{{ asset('storage/'. $game->team2->logo) }}" alt="{{ $game->team2->name }}" class="h-full w-full object-contain" loading="lazy">
                      @else
                        <span class="text-xs text-gray-500">No Logo</span>
                      @endif
                    </div>
                    <a href="{{ route('lbs.team.overview', $game->team2->id) }}"
                       class="text-sm font-semibold transition {{ $isWinner2 ? 'text-[#84CC16]' : 'hover:text-[#84CC16]' }}">
                      {{ $game->team2->name }}
                    </a>
                  </div>
                </div>

                <div class="mt-4 text-center text-sm text-[#F3F4F6]/70">
                  🗓 {{ optional($game->date)->format('d.m.Y H:i') }}
                  @if(!empty($game->venue))
                    · 📍 {{ $game->venue }}
                  @endif
                </div>

                <div class="mt-4">
                  <a href="{{ route('lbs.game.detail', $game->id) }}"
                     class="inline-flex items-center justify-center w-full px-4 py-2 rounded-lg bg-white/10 text-white font-semibold hover:bg-white/20 transition">
                    Skatīt detalizētu statistiku
                  </a>
                </div>
              </article>
            @endforeach
          </div>
        </section>
      @endif
    @endif
  </main>
</body>
</html>
