@extends('layouts.app')
@section('title', "{$game->team1->name} vs {$game->team2->name}")

@php
  // Try to resolve a sub-league to show tabs (optional)
  $subLeague = $subLeague
    ?? ($game->team1->league ?? null)
    ?? ($game->team2->league ?? null);

  // Scores (fallbacks)
  $s1 = $team1Score ?? $game->score1 ?? null;
  $s2 = $team2Score ?? $game->score2 ?? null;

  use Illuminate\Support\Facades\Storage;
  $t1LogoOk = $game->team1?->logo && Storage::disk('public')->exists($game->team1->logo);
  $t2LogoOk = $game->team2?->logo && Storage::disk('public')->exists($game->team2->logo);
@endphp

{{-- Subnav (only if we have a sub-league) --}}
@section('subnav')
  @if($subLeague)
    <x-lbs-subnav :subLeague="$subLeague" />
  @endif
@endsection

@section('content')
  <div class="max-w-6xl mx-auto px-4 space-y-12">
<br>
    {{-- Back button --}}
    <div>
      <button
        type="button"
        onclick="(document.referrer && document.referrer !== window.location.href) ? history.back() : (window.location.href='{{ route('lbs.home') }}')"
        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#84CC16] text-[#111827] font-semibold hover:bg-[#a3e635] transition"
      >
        ← Atpakaļ
      </button>
    </div>

    {{-- Game header --}}
    <section class="bg-[#1f2937] rounded-xl shadow p-6 border border-[#374151]">
      <div class="flex items-center justify-center gap-10">
        {{-- Team 1 --}}
        <div class="flex flex-col items-center">
          <a href="{{ route('lbs.team.show', $game->team1->id) }}" class="group">
            <div class="h-20 w-20 rounded bg-white grid place-items-center overflow-hidden mb-2">
              @if($t1LogoOk)
                <img src="{{ asset('storage/'.$game->team1->logo) }}" alt="{{ $game->team1->name }}" class="h-full w-full object-contain group-hover:scale-105 transition">
              @endif
            </div>
            <h2 class="text-lg font-bold group-hover:text-[#84CC16] transition text-center max-w-[12rem]">
              {{ $game->team1->name }}
            </h2>
          </a>
        </div>

        {{-- Score --}}
        <div class="text-4xl font-extrabold text-white tabular-nums">
          {{ ($s1 !== null ? $s1 : '—') }} : {{ ($s2 !== null ? $s2 : '—') }}
        </div>

        {{-- Team 2 --}}
        <div class="flex flex-col items-center">
          <a href="{{ route('lbs.team.show', $game->team2->id) }}" class="group">
            <div class="h-20 w-20 rounded bg-white grid place-items-center overflow-hidden mb-2">
              @if($t2LogoOk)
                <img src="{{ asset('storage/'.$game->team2->logo) }}" alt="{{ $game->team2->name }}" class="h-full w-full object-contain group-hover:scale-105 transition">
              @endif
            </div>
            <h2 class="text-lg font-bold group-hover:text-[#84CC16] transition text-center max-w-[12rem]">
              {{ $game->team2->name }}
            </h2>
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
                @foreach(($playerStats[$game->team1->id] ?? []) as $stat)
                  <tr class="hover:bg-[#2d3748] transition">
                    <td class="px-3 py-2">
                      @if(!empty($stat->player))
                        <a href="{{ route('lbs.player.show', $stat->player->id) }}" class="hover:text-[#84CC16]">
                          {{ $stat->player->name }}
                        </a>
                      @else
                        <span class="text-[#F3F4F6]">—</span>
                      @endif
                    </td>
                    <td class="px-3 py-2 text-right">{{ $stat->points ?? '—' }}</td>
                    <td class="px-3 py-2 text-right">{{ $stat->reb ?? '—' }}</td>
                    <td class="px-3 py-2 text-right">{{ $stat->ast ?? '—' }}</td>
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
                @foreach(($playerStats[$game->team2->id] ?? []) as $stat)
                  <tr class="hover:bg-[#2d3748] transition">
                    <td class="px-3 py-2">
                      @if(!empty($stat->player))
                        <a href="{{ route('lbs.player.show', $stat->player->id) }}" class="hover:text-[#84CC16]">
                          {{ $stat->player->name }}
                        </a>
                      @else
                        <span class="text-[#F3F4F6]">—</span>
                      @endif
                    </td>
                    <td class="px-3 py-2 text-right">{{ $stat->points ?? '—' }}</td>
                    <td class="px-3 py-2 text-right">{{ $stat->reb ?? '—' }}</td>
                    <td class="px-3 py-2 text-right">{{ $stat->ast ?? '—' }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>

  </div>
@endsection
