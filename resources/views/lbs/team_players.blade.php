<!DOCTYPE html>
<html lang="lv" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $team->name }} - Spēlētāji</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="antialiased bg-[#111827] text-[#F3F4F6]">

<x-team-navbar :parentLeagues="$parentLeagues" :team="$team" />



  <main class="pt-32 max-w-6xl mx-auto px-4">

    <!-- Team Logo + Name -->
    <div class="flex flex-col items-center space-y-4">
      @if($team->logo)
        <img src="{{ asset('storage/' . $team->logo) }}"
             alt="{{ $team->name }}"
             class="h-24 w-24 object-contain rounded shadow bg-white p-2">
      @endif
      <h1 class="text-3xl font-bold text-white">{{ $team->name }}</h1>
    </div>

    <h2 class="text-2xl font-semibold text-white mt-8">Spēlētāji</h2>

    @if($team->players->isEmpty())
      <p class="mt-2 text-[#F3F4F6]/70 text-center">Šai komandai nav pievienotu spēlētāju.</p>
    @else
    <ul class="mt-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
  @foreach($team->players as $player)
    <li>
      <a href="{{ route('lbs.player.show', $player->id) }}" 
         class="p-6 bg-[#1f2937] shadow rounded-lg flex flex-col items-center border border-[#374151] hover:border-[#84CC16] transition">
        
        @if($player->photo && file_exists(storage_path('app/public/' . $player->photo)))
          <img src="{{ asset('storage/' . $player->photo) }}" 
               alt="{{ $player->name }}" 
               class="h-20 w-20 object-cover rounded-full mb-3 border-2 border-[#84CC16]/50">
        @elseif($player->photo_url)
          <img src="{{ $player->photo_url }}" 
               alt="{{ $player->name }}" 
               class="h-20 w-20 object-cover rounded-full mb-3 border-2 border-[#84CC16]/50">
        @else
          <div class="h-20 w-20 bg-gray-700 rounded-full mb-3 flex items-center justify-center text-gray-400">
            No Photo
          </div>
        @endif

        <p class="font-medium text-lg text-white">{{ $player->name }}</p>

        @if($player->jersey_number)
          <p class="text-[#84CC16] font-semibold">#{{ $player->jersey_number }}</p>
        @endif

        @if($player->height)
          <p class="text-sm text-[#F3F4F6]/70">{{ $player->height }} cm</p>
        @endif
      </a>
    </li>
  @endforeach
</ul>

    @endif
  </main>
</body>
</html>
