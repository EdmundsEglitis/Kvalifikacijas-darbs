<!DOCTYPE html>
<html lang="lv" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $team->name }} - Komandas pārskats</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="antialiased bg-[#111827] text-[#F3F4F6]">

<x-team-navbar :parentLeagues="$parentLeagues" :team="$team" />

  <!-- Page Content -->
  <main class="pt-32 max-w-6xl mx-auto px-4 space-y-12">

    <!-- Team Header -->
    <section class="flex items-center space-x-4 mt-4">
      @if($team->logo)
        <img src="{{ asset('storage/' . $team->logo) }}"
             alt="{{ $team->name }}"
             class="h-24 w-24 object-contain rounded shadow bg-white p-2">
      @endif
      <h1 class="text-3xl font-bold text-white">{{ $team->name }}</h1>
    </section>

    <!-- Team Record -->
    <section>
      <h2 class="text-2xl font-semibold text-white">Komandas rezultāts</h2>
      <div class="mt-4 flex space-x-6">
        <div class="p-4 bg-[#1f2937] shadow rounded-lg text-center w-32 border border-[#374151]">
          <p class="text-lg font-bold text-[#84CC16]">{{ $wins }}</p>
          <p class="text-sm text-[#F3F4F6]/70">Uzvaras</p>
        </div>
        <div class="p-4 bg-[#1f2937] shadow rounded-lg text-center w-32 border border-[#374151]">
          <p class="text-lg font-bold text-[#F97316]">{{ $losses }}</p>
          <p class="text-sm text-[#F3F4F6]/70">Zaudējumi</p>
        </div>
      </div>
    </section>

    <!-- Average Team Stats -->
    <section>
      <h2 class="text-2xl font-semibold text-white">Vidējie komandas statistikas rādītāji</h2>

      @if(empty($averageStats))
        <p class="mt-2 text-[#F3F4F6]/70">Nav pieejamas statistikas.</p>
      @else
        <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
          @foreach(['points'=>'Punkti','reb'=>'Atlēkušās','ast'=>'Piespēles','stl'=>'Pārķertās','blk'=>'Bloki'] as $key => $label)
            <div class="p-4 bg-[#1f2937] shadow rounded-lg text-center border border-[#374151]">
              <p class="text-lg font-bold text-[#84CC16]">{{ number_format($averageStats[$key], 1) }}</p>
              <p class="text-sm text-[#F3F4F6]/70">{{ $label }}</p>
            </div>
          @endforeach
        </div>
      @endif
    </section>

<!-- Best Players -->
<section>
  <h2 class="text-2xl font-semibold text-white">Labākie spēlētāji</h2>

  @if(empty($bestPlayers) || collect($bestPlayers)->every(fn($player) => is_null($player)))
    <p class="mt-2 text-[#F3F4F6]/70">Nav pieejamu spēlētāju datu.</p>
  @else
    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
      @foreach($bestPlayers as $stat => $player)
        @if($player)
          <div class="p-4 bg-[#1f2937] shadow rounded-lg border border-[#374151]">
            <h3 class="text-lg font-medium text-[#F3F4F6]/90">{{ ucfirst($stat) }} līderis</h3>
            <a href="{{ route('lbs.player.show', $player->id) }}" 
               class="mt-2 font-semibold text-white hover:text-[#84CC16] block">
              {{ $player->name }}
            </a>
            <p class="text-[#84CC16]">{{ $player->value }} {{ $stat }}</p>
          </div>
        @endif
      @endforeach
    </div>
  @endif
</section>



  </main>
</body>
</html>
