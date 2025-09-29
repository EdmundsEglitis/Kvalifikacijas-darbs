<!DOCTYPE html>
<html lang="lv" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $team->name }} — Komandas pārskats</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="antialiased bg-[#111827] text-[#F3F4F6]">

  {{-- Main + sub tabs for this team --}}
  <x-team-navbar :parentLeagues="$parentLeagues" :team="$team" />

  <main class="pt-32 max-w-6xl mx-auto px-4 space-y-12">

    {{-- Team Header --}}
    <section class="bg-[#1f2937] border border-[#374151] rounded-2xl p-6 shadow">
      <div class="flex items-center gap-5">
        <div class="h-24 w-24 rounded-xl bg-[#111827] grid place-items-center overflow-hidden ring-2 ring-[#84CC16]/40">
          @if($team->logo)
            <img src="{{ asset('storage/' . $team->logo) }}"
                 alt="{{ $team->name }}"
                 class="h-full w-full object-contain"
                 loading="lazy">
          @else
            <span class="text-xs text-gray-400">No Logo</span>
          @endif
        </div>

        <div class="flex-1">
          <h1 class="text-3xl font-extrabold text-white">{{ $team->name }}</h1>

          {{-- W-L pill --}}
          <div class="mt-3 flex flex-wrap items-center gap-3">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#84CC16]/15 border border-[#84CC16]/30 text-[#84CC16] text-sm font-semibold">
              <span class="h-2 w-2 rounded-full bg-[#84CC16]"></span>
              Bilance: <span class="tabular-nums">{{ (int)($wins ?? 0) }}–{{ (int)($losses ?? 0) }}</span>
            </span>
          </div>
        </div>
      </div>
    </section>

    {{-- Team Record (compact cards) --}}
    <section>
      <h2 class="text-2xl font-semibold text-white mb-4">Komandas rezultāts</h2>

      <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-4">
        <div class="min-h-[96px] p-4 bg-[#1f2937] border border-[#374151] rounded-xl text-center shadow hover:border-[#84CC16] transition">
          <p class="text-2xl font-extrabold text-[#84CC16] tabular-nums">{{ (int)($wins ?? 0) }}</p>
          <p class="text-xs text-[#F3F4F6]/70 mt-1">Uzvaras</p>
        </div>
        <div class="min-h-[96px] p-4 bg-[#1f2937] border border-[#374151] rounded-xl text-center shadow hover:border-[#F97316] transition">
          <p class="text-2xl font-extrabold text-[#F97316] tabular-nums">{{ (int)($losses ?? 0) }}</p>
          <p class="text-xs text-[#F3F4F6]/70 mt-1">Zaudējumi</p>
        </div>
      </div>
    </section>

    {{-- Average Team Stats --}}
    <section>
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-2xl font-semibold text-white">Vidējie komandas rādītāji</h2>
        @php
          $hasStats = !empty($averageStats) && collect(['points','reb','ast','stl','blk'])->some(fn($k) => isset($averageStats[$k]));
        @endphp
        @if($hasStats)
          <span class="text-xs px-2 py-1 rounded-full bg-white/10 text-white">Sezona</span>
        @endif
      </div>

      @if(!$hasStats)
        <p class="text-[#F3F4F6]/70">Nav pieejamas statistikas.</p>
      @else
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
          @foreach(['points'=>'Punkti','reb'=>'Atlēkušās','ast'=>'Piespēles','stl'=>'Pārķertās','blk'=>'Bloki'] as $key => $label)
            <div class="min-h-[110px] p-4 bg-[#1f2937] border border-[#374151] rounded-xl text-center shadow hover:-translate-y-0.5 hover:shadow-lg transition">
              <p class="text-2xl font-extrabold text-[#84CC16] tabular-nums">
                {{ isset($averageStats[$key]) ? number_format((float)$averageStats[$key], 1) : '—' }}
              </p>
              <p class="text-xs text-[#F3F4F6]/70 mt-1">{{ $label }}</p>
            </div>
          @endforeach
        </div>
      @endif
    </section>

    {{-- Best Players --}}
    <section>
      <h2 class="text-2xl font-semibold text-white mb-4">Labākie spēlētāji</h2>

      @php
        $hasLeaders = !empty($bestPlayers) && collect($bestPlayers)->filter()->isNotEmpty();
      @endphp

      @if(!$hasLeaders)
        <p class="text-[#F3F4F6]/70">Nav pieejamu spēlētāju datu.</p>
      @else
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
          @foreach($bestPlayers as $stat => $player)
            @if($player)
              <div class="min-h-[140px] p-5 bg-[#1f2937] border border-[#374151] rounded-xl shadow hover:border-[#84CC16] transition">
                <div class="text-xs uppercase tracking-wide text-[#F3F4F6]/60">{{ ucfirst($stat) }} līderis</div>
                <a href="{{ route('lbs.player.show', $player->id) }}"
                   class="block mt-2 text-lg font-bold text-white hover:text-[#84CC16] transition">
                  {{ $player->name }}
                </a>
                <p class="mt-1 text-[#84CC16] font-semibold">
                  {{ is_numeric($player->value) ? number_format((float)$player->value, 1) : $player->value }} {{ $stat }}
                </p>
              </div>
            @endif
          @endforeach
        </div>
      @endif
    </section>

  </main>
</body>
</html>
