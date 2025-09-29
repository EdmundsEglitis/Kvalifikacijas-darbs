<!DOCTYPE html>
<html lang="lv" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $game->team1->name }} vs {{ $game->team2->name }}</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="antialiased bg-[#111827] text-[#F3F4F6]">

  {{-- Navbar with back button --}}
  <x-sub-league-tabs :parentLeagues="$parentLeagues" :subLeague="$game->team1->league ?? null">
    <a href="{{ url()->previous() }}" 
       class="ml-4 px-3 py-2 rounded bg-[#84CC16] text-[#111827] font-semibold hover:bg-[#a3e635] transition">
      ← Atpakaļ
    </a>
  </x-sub-league-tabs>

  <main class="pt-32 max-w-6xl mx-auto px-4 space-y-12">
    
    {{-- Game header --}}
    <section class="bg-[#1f2937] rounded-xl shadow p-6 border border-[#374151]">
      <div class="flex items-center justify-center gap-10">
        {{-- Team 1 --}}
        <div class="flex flex-col items-center">
          <a href="{{ route('lbs.team.show', $game->team1->id) }}" class="group">
            <img src="{{ asset('storage/' . $game->team1->logo) }}" 
                 alt="{{ $game->team1->name }}" 
                 class="h-20 w-20 object-contain mb-2 bg-white rounded shadow group-hover:scale-105 transition">
            <h2 class="text-lg font-bold group-hover:text-[#84CC16] transition">{{ $game->team1->name }}</h2>
          </a>
        </div>

        {{-- Score --}}
        <div class="text-4xl font-extrabold text-white">
          {{ $team1Score }} : {{ $team2Score }}
        </div>

        {{-- Team 2 --}}
        <div class="flex flex-col items-center">
          <a href="{{ route('lbs.team.show', $game->team2->id) }}" class="group">
            <img src="{{ asset('storage/' . $game->team2->logo) }}" 
                 alt="{{ $game->team2->name }}" 
                 class="h-20 w-20 object-contain mb-2 bg-white rounded shadow group-hover:scale-105 transition">
            <h2 class="text-lg font-bold group-hover:text-[#84CC16] transition">{{ $game->team2->name }}</h2>
          </a>
        </div>
      </div>

      <div class="mt-4 text-center text-sm text-[#F3F4F6]/70">
        🗓 {{ \Carbon\Carbon::parse($game->date)->format('d.m.Y H:i') }}
        @if(!empty($game->venue)) · 📍 {{ $game->venue }} @endif
      </div>
    </section>

    {{-- Player stats per team --}}
    <section>
      <h2 class="text-2xl font-bold text-white mb-6">Spēlētāju statistika</h2>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        {{-- Team 1 stats --}}
        <div>
          <h3 class="text-xl font-semibold mb-3">{{ $game->team1->name }}</h3>
          <div class="overflow-x-auto">
            <table class="min-w-full bg-[#1f2937] border border-[#374151] rounded">
              <thead class="bg-[#374151] text-xs uppercase text-[#F3F4F6]/70">
                <tr>
                  <th class="px-3 py-2 text-left">Spēlētājs</th>
                  <th class="px-3 py-2 text-right">Punkti</th>
                  <th class="px-3 py-2 text-right">Atl.</th>
                  <th class="px-3 py-2 text-right">Piesp.</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-[#374151]">
                @foreach($playerStats[$game->team1->id] ?? [] as $stat)
                  <tr class="hover:bg-[#2d3748] transition">
                    <td class="px-3 py-2">
                      <a href="{{ route('lbs.player.show', $stat->player->id) }}" 
                         class="hover:text-[#84CC16]">
                        {{ $stat->player->name }}
                      </a>
                    </td>
                    <td class="px-3 py-2 text-right">{{ $stat->points }}</td>
                    <td class="px-3 py-2 text-right">{{ $stat->reb }}</td>
                    <td class="px-3 py-2 text-right">{{ $stat->ast }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>

        {{-- Team 2 stats --}}
        <div>
          <h3 class="text-xl font-semibold mb-3">{{ $game->team2->name }}</h3>
          <div class="overflow-x-auto">
            <table class="min-w-full bg-[#1f2937] border border-[#374151] rounded">
              <thead class="bg-[#374151] text-xs uppercase text-[#F3F4F6]/70">
                <tr>
                  <th class="px-3 py-2 text-left">Spēlētājs</th>
                  <th class="px-3 py-2 text-right">Punkti</th>
                  <th class="px-3 py-2 text-right">Atl.</th>
                  <th class="px-3 py-2 text-right">Piesp.</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-[#374151]">
                @foreach($playerStats[$game->team2->id] ?? [] as $stat)
                  <tr class="hover:bg-[#2d3748] transition">
                    <td class="px-3 py-2">
                      <a href="{{ route('lbs.player.show', $stat->player->id) }}" 
                         class="hover:text-[#84CC16]">
                        {{ $stat->player->name }}
                      </a>
                    </td>
                    <td class="px-3 py-2 text-right">{{ $stat->points }}</td>
                    <td class="px-3 py-2 text-right">{{ $stat->reb }}</td>
                    <td class="px-3 py-2 text-right">{{ $stat->ast }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>

  </main>
</body>
</html>
