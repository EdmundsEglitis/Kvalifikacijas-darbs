@extends('layouts.app')
@section('title', $player->name . ' – Spēlētāja profils')

{{-- Subnav: team tabs below the main navbar (only if the player has a team) --}}
@section('subnav')
  @if(!empty($player->team))
  <x-teamnav :team="$player->team" />
  @endif
@endsection

@section('content')
  @php
    // Safe defaults for totals/averages
    $games    = (int) ($totals['games'] ?? 0);
    $avg      = fn($k) => isset($averages[$k]) ? $averages[$k] : '—';
  @endphp
<br>
  <div class="max-w-6xl mx-auto px-4 space-y-12 sm:space-y-16">

    <section class="grid grid-cols-1 md:grid-cols-[auto,1fr] items-start gap-6 md:gap-8">
      <div class="flex md:block justify-center">
        @if(!empty($player->photo))
          <img
            src="{{ asset('storage/' . $player->photo) }}"
            alt="{{ $player->name }}"
            class="h-28 w-28 sm:h-36 sm:w-36 rounded-full border-4 border-[#84CC16]/60 shadow-lg object-cover"
          >
        @else
          <div class="h-28 w-28 sm:h-36 sm:w-36 rounded-full bg-gray-700 flex items-center justify-center text-gray-400 text-xs sm:text-sm">
            No Photo
          </div>
        @endif
      </div>

      <div class="space-y-4 text-center md:text-left">
        <div>
          <h1 class="text-2xl sm:text-4xl font-extrabold text-white leading-tight">{{ $player->name }}</h1>
          @if(!empty($player->team))
            <p class="mt-1 text-[#9CA3AF]">
              Komanda:
              <a href="{{ route('lbs.team.show', $player->team->id) }}"
                 class="text-[#84CC16] hover:underline font-medium">
                {{ $player->team->name }}
              </a>
            </p>
          @endif
        </div>

        <div class="flex flex-wrap items-center justify-center md:justify-start gap-2 sm:gap-3 text-sm text-[#9CA3AF]">
          <span class="px-2.5 py-1 rounded-full bg-white/5 border border-white/10">
            Numurs: <span class="font-semibold text-white">{{ $player->jersey_number ?? '—' }}</span>
          </span>
          <span class="px-2.5 py-1 rounded-full bg-white/5 border border-white/10">
            Augums: <span class="font-semibold text-white">{{ $player->height ?? '—' }} cm</span>
          </span>
          <span class="px-2.5 py-1 rounded-full bg-white/5 border border-white/10">
            Dzimšanas diena: <span class="font-semibold text-white">{{ $player->birthday ?? '—' }}</span>
          </span>
          <span class="px-2.5 py-1 rounded-full bg-white/5 border border-white/10">
            Tautība: <span class="font-semibold text-white">{{ $player->nationality ?? '—' }}</span>
          </span>
        </div>
      </div>
    </section>

    <section>
      <h2 class="text-xl sm:text-2xl font-bold text-white mb-4 sm:mb-6">Sezonas kopsavilkums</h2>

      @if($games > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
          @foreach([
            ['label' => 'Spēles',            'value' => $games],
            ['label' => 'Vid. punkti',       'value' => $avg('points')],
            ['label' => 'Vid. atl. bumbas',  'value' => $avg('reb')],
            ['label' => 'Vid. piespēles',    'value' => $avg('ast')],
            ['label' => 'Vid. pārtvertās',   'value' => $avg('stl')],
            ['label' => 'Vid. efektivitāte', 'value' => $avg('eff')],
          ] as $stat)
            <div class="bg-[#1f2937] p-4 sm:p-5 rounded-xl text-center shadow border border-[#374151] hover:border-[#84CC16] transition">
              <div class="text-xs sm:text-sm text-[#9CA3AF]">{{ $stat['label'] }}</div>
              <div class="mt-0.5 text-xl sm:text-2xl font-extrabold text-[#84CC16]">
                {{ is_numeric($stat['value']) ? (str_contains((string)$stat['value'], '.') ? number_format((float)$stat['value'], 1) : $stat['value']) : $stat['value'] }}
              </div>
            </div>
          @endforeach
        </div>
      @else
        <p class="text-[#F3F4F6]/70">Nav pieejamas statistikas.</p>
      @endif
    </section>

    <section>
      <h2 class="text-xl sm:text-2xl font-bold text-white mb-4 sm:mb-6">Spēļu statistika</h2>

      <div class="space-y-3 sm:space-y-4 md:hidden">
        @foreach($player->playerGameStats as $stat)
          @php
            $g = $stat->game ?? null;
            if (!$g) continue;
            $opponent = ($g->team1?->id ?? null) === ($player->team_id ?? null) ? ($g->team2 ?? null) : ($g->team1 ?? null);
          @endphp
          <article class="bg-[#1f2937] border border-[#374151] rounded-xl p-4 shadow hover:shadow-lg transition">
            <div class="flex items-start justify-between gap-3">
              <div>
                <div class="text-sm text-gray-300">{{ optional($g->date)->format('d.m.Y') }}</div>
                @if($opponent)
                  <a href="{{ route('lbs.team.show', $opponent->id) }}"
                     class="block text-base font-semibold text-white hover:text-[#84CC16] truncate">
                    {{ $opponent->name }}
                  </a>
                @endif
              </div>
              <div class="text-right">
                <div class="text-xs text-gray-400">Punkti</div>
                <div class="text-lg font-bold text-[#84CC16]">{{ $stat->points ?? '—' }}</div>
              </div>
            </div>

            <dl class="mt-3 grid grid-cols-3 gap-2 text-xs">
              @foreach([
                ['Min',   $stat->minutes ?? '—'],
                ['2PM/2PA', ($stat->fgm2 ?? '—') . '/' . ($stat->fga2 ?? '—')],
                ['3PM/3PA', ($stat->fgm3 ?? '—') . '/' . ($stat->fga3 ?? '—')],
                ['FTM/FTA', ($stat->ftm  ?? '—') . '/' . ($stat->fta  ?? '—')],
                ['REB',  $stat->reb ?? '—'],
                ['AST',  $stat->ast ?? '—'],
                ['STL',  $stat->stl ?? '—'],
                ['BLK',  $stat->blk ?? '—'],
                ['TOV',  $stat->tov ?? '—'],
                ['PF',   $stat->pf  ?? '—'],
              ] as [$label,$val])
                <div class="rounded-lg bg-white/5 border border-white/10 p-2 text-center">
                  <dt class="text-gray-400">{{ $label }}</dt>
                  <dd class="font-semibold">{{ $val }}</dd>
                </div>
              @endforeach
              <div class="rounded-lg bg-white/5 border border-white/10 p-2 text-center col-span-3">
                <dt class="text-gray-400">EFF</dt><dd class="font-semibold">{{ $stat->eff ?? '—' }}</dd>
              </div>
            </dl>
          </article>
        @endforeach
      </div>

      <div class="hidden md:block overflow-x-auto rounded-xl shadow-lg border border-[#374151]">
        <table class="min-w-full divide-y divide-[#374151]">
          <thead class="bg-[#0f172a] sticky top-0 z-10">
            <tr>
              @foreach(['Datums','Pretinieks','Min','Punkti','2PM/2PA','3PM/3PA','FTM/FTA','REB','AST','STL','BLK','TOV','PF','EFF'] as $head)
                <th class="px-3 py-3 text-xs font-semibold text-[#F3F4F6]/70 uppercase text-center">{{ $head }}</th>
              @endforeach
            </tr>
          </thead>
          <tbody class="divide-y divide-[#374151]">
            @foreach($player->playerGameStats as $stat)
              @php
                $g = $stat->game ?? null;
                if (!$g) continue;
                $opponent = ($g->team1?->id ?? null) === ($player->team_id ?? null) ? ($g->team2 ?? null) : ($g->team1 ?? null);
              @endphp
              <tr class="hover:bg-[#223041] transition">
                <td class="px-3 py-2 text-sm text-center whitespace-nowrap">{{ optional($g->date)->format('d.m.Y') }}</td>
                <td class="px-3 py-2 text-sm text-center">
                  @if($opponent)
                    <a href="{{ route('lbs.team.show', $opponent->id) }}" class="hover:text-[#84CC16] font-medium">
                      {{ $opponent->name }}
                    </a>
                  @else
                    —
                  @endif
                </td>
                <td class="px-3 py-2 text-center">{{ $stat->minutes ?? '—' }}</td>
                <td class="px-3 py-2 text-center font-semibold text-[#84CC16]">{{ $stat->points ?? '—' }}</td>
                <td class="px-3 py-2 text-center">{{ ($stat->fgm2 ?? '—') }}/{{ ($stat->fga2 ?? '—') }}</td>
                <td class="px-3 py-2 text-center">{{ ($stat->fgm3 ?? '—') }}/{{ ($stat->fga3 ?? '—') }}</td>
                <td class="px-3 py-2 text-center">{{ ($stat->ftm  ?? '—') }}/{{ ($stat->fta  ?? '—') }}</td>
                <td class="px-3 py-2 text-center">{{ $stat->reb ?? '—' }}</td>
                <td class="px-3 py-2 text-center">{{ $stat->ast ?? '—' }}</td>
                <td class="px-3 py-2 text-center">{{ $stat->stl ?? '—' }}</td>
                <td class="px-3 py-2 text-center">{{ $stat->blk ?? '—' }}</td>
                <td class="px-3 py-2 text-center">{{ $stat->tov ?? '—' }}</td>
                <td class="px-3 py-2 text-center">{{ $stat->pf  ?? '—' }}</td>
                <td class="px-3 py-2 text-center font-semibold">{{ $stat->eff ?? '—' }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </section>

  </div>
@endsection
