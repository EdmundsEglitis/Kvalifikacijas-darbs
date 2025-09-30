@extends('layouts.app')
@section('title', $team->name . ' – Komandas statistika')

{{-- Subnav (team tabs) under the main navbar --}}
@section('subnav')
<x-teamnav :team="$team" />
@endsection

@section('content')
  <div class="max-w-7xl mx-auto px-4 space-y-16">

    {{-- Team Logo + Name --}}
    <section class="flex flex-col items-center space-y-4 text-center">
      @php
        $hasLogo = $team->logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($team->logo);
      @endphp
      @if($hasLogo)
        <img
          src="{{ asset('storage/' . $team->logo) }}"
          alt="{{ $team->name }}"
          class="h-28 w-28 object-contain shadow-lg bg-[#111827] rounded-xl ring-2 ring-[#84CC16]/40"
        >
      @endif
      <h1 class="text-4xl font-extrabold text-white drop-shadow">{{ $team->name }}</h1>
      <p class="text-[#F3F4F6]/70 text-sm">Komandas pārskats & statistika</p>
    </section>

    {{-- Team Record --}}
    <section>
      <h2 class="text-2xl font-bold text-white mb-6">Komandas rezultāti</h2>
      <div class="flex justify-center gap-6">
        <div class="p-6 bg-[#1f2937] rounded-xl shadow-lg text-center w-40 border border-[#374151] hover:border-[#84CC16] transition">
          <p class="text-3xl font-extrabold text-[#84CC16]">{{ (int) $wins }}</p>
          <p class="mt-1 text-sm text-[#F3F4F6]/80">Uzvaras</p>
        </div>
        <div class="p-6 bg-[#1f2937] rounded-xl shadow-lg text-center w-40 border border-[#374151] hover:border-[#F97316] transition">
          <p class="text-3xl font-extrabold text-[#F97316]">{{ (int) $losses }}</p>
          <p class="mt-1 text-sm text-[#F3F4F6]/80">Zaudējumi</p>
        </div>
      </div>
    </section>

    {{-- Average Team Stats --}}
    <section>
      <h2 class="text-2xl font-bold text-white mb-6">Vidējie rādītāji vidējā spēlē</h2>
      <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-6">
        @foreach($averageStats as $stat)
          <div class="p-5 bg-[#1f2937] rounded-xl shadow border border-[#374151] hover:border-[#84CC16] transition flex flex-col items-center">
            <p class="text-2xl font-bold text-[#84CC16]">{{ number_format((float)($stat['avg'] ?? 0), 1) }}</p>
            <p class="text-sm text-[#F3F4F6]/70 mt-1">{{ $stat['label'] ?? '' }}</p>
          </div>
        @endforeach
      </div>
    </section>

    {{-- Player Stats Table --}}
    <section>
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-white">Spēlētāju statistika</h2>
        <span class="px-3 py-1 text-xs rounded-full bg-[#84CC16]/20 text-[#84CC16] font-semibold">
          {{ $playersStats->count() }} spēlētāji
        </span>
      </div>

      <div class="overflow-x-auto rounded-xl shadow-lg border border-[#374151] bg-[#1f2937]">
        <table class="min-w-full divide-y divide-[#374151]">
          <thead class="bg-[#0f172a] sticky top-0 z-10">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-semibold text-[#F3F4F6]/70 uppercase">Spēlētājs</th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-[#F3F4F6]/70 uppercase">PPG</th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-[#F3F4F6]/70 uppercase">G</th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-[#F3F4F6]/70 uppercase">Min</th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-[#F3F4F6]/70 uppercase">RPG</th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-[#F3F4F6]/70 uppercase">APG</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#374151]">
            @foreach($playersStats as $p)
              @php
                $hasPhoto = !empty($p['photo']) && \Illuminate\Support\Facades\Storage::disk('public')->exists($p['photo']);
                $mins = (float)($p['minutes'] ?? 0);
                // If minutes are total minutes, format as mm:ss; if already per-game decimal, show with 1 dec.
                $minsText = $mins > 120 ? gmdate('i:s', (int) round($mins)) : number_format($mins, 1);
              @endphp
              <tr class="hover:bg-[#2d3748] transition">
                <td class="px-4 py-3">
                  <a href="{{ route('lbs.player.show', $p['id']) }}" class="flex items-center gap-3 hover:text-[#84CC16]">
                    @if($hasPhoto)
                      <img
                        src="{{ asset('storage/' . $p['photo']) }}"
                        alt="{{ $p['name'] }}"
                        class="h-9 w-9 object-cover rounded-full border border-[#84CC16]/40"
                      >
                    @else
                      <div class="h-9 w-9 rounded-full bg-gray-600 flex items-center justify-center text-xs text-gray-300">?</div>
                    @endif
                    <span class="font-medium text-white">{{ $p['name'] }}</span>
                  </a>
                </td>
                <td class="px-4 py-3 text-right text-[#84CC16] font-semibold">{{ number_format((float)($p['ppg'] ?? 0), 1) }}</td>
                <td class="px-4 py-3 text-right">{{ (int)($p['gamesPlayed'] ?? 0) }}</td>
                <td class="px-4 py-3 text-right">{{ $minsText }}</td>
                <td class="px-4 py-3 text-right">{{ number_format((float)($p['rpg'] ?? 0), 1) }}</td>
                <td class="px-4 py-3 text-right">{{ number_format((float)($p['apg'] ?? 0), 1) }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </section>

  </div>
@endsection
