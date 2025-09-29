<!DOCTYPE html>
<html lang="lv" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $team->name }} — Spēles</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="antialiased bg-[#111827] text-[#F3F4F6]">

  {{-- Main + Sub Tabs --}}
  <x-team-navbar :parentLeagues="$parentLeagues" :team="$team" />

  <main class="pt-32 max-w-6xl mx-auto px-4 space-y-12">

    {{-- Team Record --}}
    <section>
      <h2 class="text-2xl font-bold text-white mb-4">Komandas rezultāts</h2>
      @php
        $wins = $games->where('winner_id', $team->id)->count();
        $losses = $games->count() - $wins;
      @endphp
      <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-4">
        <div class="min-h-[96px] p-4 bg-[#1f2937] border border-[#374151] rounded-xl text-center shadow hover:border-[#84CC16] transition">
          <p class="text-2xl font-extrabold text-[#84CC16] tabular-nums">{{ $wins }}</p>
          <p class="text-xs text-[#F3F4F6]/70 mt-1">Uzvaras</p>
        </div>
        <div class="min-h-[96px] p-4 bg-[#1f2937] border border-[#374151] rounded-xl text-center shadow hover:border-[#F97316] transition">
          <p class="text-2xl font-extrabold text-[#F97316] tabular-nums">{{ $losses }}</p>
          <p class="text-xs text-[#F3F4F6]/70 mt-1">Zaudējumi</p>
        </div>
      </div>
    </section>

    {{-- Games --}}
    <section>
      <h2 class="text-2xl font-bold text-white mb-4">Spēles</h2>

      @if($games->isEmpty())
        <p class="mt-2 text-[#F3F4F6]/70">Šai komandai vēl nav spēļu.</p>
      @else
        <div class="space-y-10">
          {{-- Upcoming --}}
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
                      {{-- Team 1 --}}
                      <div class="flex flex-col items-center gap-2 w-28">
                        <div class="h-16 w-16 bg-[#111827] rounded-xl grid place-items-center overflow-hidden ">
                          @if($game->team1?->logo)
                            <img src="{{ asset('storage/' . $game->team1->logo) }}" alt="{{ $game->team1->name }}" class="h-full w-full object-contain "  loading="lazy">
                          @endif
                        </div>
                        <span class="text-sm font-semibold">{{ $game->team1->name }}</span>
                      </div>

                      <div class="text-[#F3F4F6]/60 font-semibold">vs</div>

                      {{-- Team 2 --}}
                      <div class="flex flex-col items-center gap-2 w-28">
                        <div class="h-16 w-16 bg-[#111827] rounded-xl grid place-items-center overflow-hidden">
                          @if($game->team2?->logo)
                            <img src="{{ asset('storage/' . $game->team2->logo) }}" alt="{{ $game->team2->name }}" class="h-full w-full object-contain" loading="lazy">
                          @endif
                        </div>
                        <span class="text-sm font-semibold">{{ $game->team2->name }}</span>
                      </div>
                    </div>

                    <div class="mt-4 text-sm text-[#F3F4F6]/70">
                      🗓 {{ \Carbon\Carbon::parse($game->date)->format('d.m.Y H:i') }}
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

          {{-- Past --}}
          @if($pastGames->isNotEmpty())
            <div>
              <h3 class="text-xl font-semibold text-white mb-4">Aizvadītās spēles</h3>
              <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($pastGames as $game)
                  <article class="group bg-[#1f2937] border border-[#374151] rounded-2xl shadow hover:shadow-xl transition p-5 flex flex-col items-center text-center">
                    <div class="flex-1 flex items-center justify-center gap-6">
                      {{-- Team 1 --}}
                      <div class="flex flex-col items-center gap-2 w-28">
                        <div class="h-16 w-16 bg-[#111827] rounded-xl grid place-items-center overflow-hidden">
                          @if($game->team1?->logo)
                            <img src="{{ asset('storage/' . $game->team1->logo) }}" alt="{{ $game->team1->name }}" class="h-full w-full object-contain" loading="lazy">
                          @endif
                        </div>
                        <span class="text-sm font-semibold">{{ $game->team1->name }}</span>
                      </div>

                      {{-- Score --}}
                      <div class="text-center">
                        <div class="text-1xl font-extrabold text-white tabular-nums">
                          {{ $game->score1 }} : {{ $game->score2 }}
                        </div>
                        <div class="text-xs text-[#F3F4F6]/60 mt-1">Galarezultāts</div>
                      </div>

                      {{-- Team 2 --}}
                      <div class="flex flex-col items-center gap-2 w-28">
                        <div class="h-16 w-16 bg-[#111827] rounded-xl grid place-items-center overflow-hidden">
                          @if($game->team2?->logo)
                            <img src="{{ asset('storage/' . $game->team2->logo) }}" alt="{{ $game->team2->name }}" class="h-full w-full object-contain" loading="lazy">
                          @endif
                        </div>
                        <span class="text-sm font-semibold">{{ $game->team2->name }}</span>
                      </div>
                    </div>

                    <div class="mt-4 text-sm text-[#F3F4F6]/70">
                      🗓 {{ \Carbon\Carbon::parse($game->date)->format('d.m.Y H:i') }}
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

  </main>
</body>
</html>
