<!DOCTYPE html>
<html lang="lv" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $subLeague->name }} - Kalendārs</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const menuBtn = document.getElementById('menu-btn');
      const mobileMenu = document.getElementById('mobile-menu');
      if (menuBtn) {
        menuBtn.addEventListener('click', () => {
          mobileMenu.classList.toggle('hidden');
        });
      }
    });
  </script>
</head>
<body class="antialiased bg-[#111827] text-[#F3F4F6]">
<x-sub-league-tabs :parentLeagues="$parentLeagues" :subLeague="$subLeague" />

<main class="pt-32 max-w-4xl mx-auto px-4">
  <h1 class="text-3xl font-bold text-white">{{ $subLeague->name }} - Kalendārs</h1>

  @if($games->isEmpty())
    <p class="mt-4 text-[#F3F4F6]/70">Nav pieejamu spēļu.</p>
  @else
    <div class="mt-6 space-y-6">

      {{-- Upcoming Games --}}
      @foreach($upcomingGames as $game)
        <div class="p-6 shadow rounded-lg flex flex-col items-center text-center bg-yellow-100 border-2 border-yellow-400">
          <span class="px-2 py-1 bg-yellow-400 text-white rounded-full text-xs font-bold mb-2">GAIDĀMĀ SPĒLE</span>
          <div class="flex items-center justify-center space-x-6">
            @foreach([$game->team1, $game->team2] as $team)
              <div class="flex flex-col items-center space-y-1">
                @if($team->logo)
                  <img src="{{ asset('storage/' . $team->logo) }}" alt="{{ $team->name }}" class="h-16 w-16 object-contain rounded">
                @endif
                <span class="font-medium text-lg text-gray-800">{{ $team->name }}</span>
              </div>
            @endforeach
          </div>
          <div class="mt-2 text-sm text-gray-700">
            Datums: {{ \Carbon\Carbon::parse($game->date)->format('d.m.Y H:i') }}
          </div>
          <a href="{{ route('lbs.game.detail', $game->id) }}" class="mt-4 px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded">
            Skatīt detalizētu statistiku
          </a>
        </div>
      @endforeach

      {{-- Past Games --}}
      @foreach($pastGames as $game)
        <div class="p-6 shadow rounded-lg flex flex-col items-center text-center bg-[#1f2937] border border-[#374151]">
          <div class="flex items-center justify-center space-x-6">
            @foreach([$game->team1, $game->team2] as $team)
              <div class="flex flex-col items-center space-y-1">
                @if($team->logo)
                  <img src="{{ asset('storage/' . $team->logo) }}" alt="{{ $team->name }}" class="h-16 w-16 object-contain rounded">
                @endif
                <span class="font-medium text-lg text-white">{{ $team->name }}</span>
              </div>
            @endforeach
          </div>
          <div class="mt-2 font-bold text-xl text-[#F3F4F6]">
            {{ $game->score1 }} : {{ $game->score2 }}
          </div>
          <div class="text-sm text-[#F3F4F6]/70 mt-1">
            Datums: {{ \Carbon\Carbon::parse($game->date)->format('d.m.Y H:i') }}
          </div>
          <a href="{{ route('lbs.game.detail', $game->id) }}" class="mt-4 px-4 py-2 bg-[#84CC16] hover:bg-[#a6e23a] text-[#111827] rounded font-semibold">
            Skatīt detalizētu statistiku
          </a>
        </div>
      @endforeach

    </div>
  @endif
</main>
