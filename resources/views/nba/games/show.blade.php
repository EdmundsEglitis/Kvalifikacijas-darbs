@extends('layouts.nba')
@section('title', 'Game Box Score')

@section('content')
<main class="max-w-7xl mx-auto px-4 py-6 space-y-6">

  {{-- Header / Scoreboard --}}
  <section class="bg-[#1f2937] border border-[#374151] rounded-xl p-4 sm:p-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div class="flex items-center gap-4">
        {{-- Team A --}}
        <div class="flex items-center gap-2">
          @if(!empty($A['logo']))
            <img src="{{ $A['logo'] }}" alt="{{ $A['team'] }} logo" class="h-8 w-8 object-contain rounded bg-white p-[2px]" />
          @else
            <div class="h-8 w-8 rounded bg-white/10"></div>
          @endif
          <div class="text-white font-semibold">{{ $A['team'] ?? '—' }}</div>
        </div>

        {{-- Score --}}
        <div class="text-2xl sm:text-3xl font-bold tabular-nums {{ $game['winner'] === 0 ? 'text-[#84CC16]' : ($game['winner'] === 1 ? 'text-[#F97316]' : 'text-white') }}">
          {{ $game['score'] }}
        </div>

        {{-- Team B --}}
        <div class="flex items-center gap-2">
          <div class="text-white font-semibold text-right">{{ $B['team'] ?? '—' }}</div>
          @if(!empty($B['logo']))
            <img src="{{ $B['logo'] }}" alt="{{ $B['team'] }} logo" class="h-8 w-8 object-contain rounded bg-white p-[2px]" />
          @else
            <div class="h-8 w-8 rounded bg-white/10"></div>
          @endif
        </div>
      </div>

      <div class="text-sm text-gray-300">
        <div>Event ID: <span class="text-white">{{ $game['event_id'] }}</span></div>
        <div>Date: <span class="text-white">{{ \Illuminate\Support\Carbon::parse($game['date'])->toFormattedDateString() }}</span></div>
      </div>
    </div>
  </section>

  {{-- Two team tables --}}
  <div class="grid gap-6 lg:grid-cols-2">

    {{-- Team A table --}}
    <section class="bg-[#1f2937] border border-[#374151] rounded-xl overflow-hidden">
      <div class="px-4 py-3 bg-[#0f172a] border-b border-[#374151] flex items-center justify-between">
        <h2 class="font-semibold">{{ $A['team'] ?? 'Team A' }}</h2>
        <div class="text-sm text-gray-300">PTS: <span class="font-semibold text-white">{{ $A['totals']['pts'] ?? '—' }}</span></div>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-[900px] w-full text-sm">
          <thead class="bg-[#0f172a] text-gray-300">
            <tr>
              <th class="px-3 py-2 text-left">Player</th>
              <th class="px-3 py-2 text-center">MIN</th>
              <th class="px-3 py-2 text-center">FG</th>
              <th class="px-3 py-2 text-center">3PT</th>
              <th class="px-3 py-2 text-center">FT</th>
              <th class="px-3 py-2 text-center">REB</th>
              <th class="px-3 py-2 text-center">AST</th>
              <th class="px-3 py-2 text-center">STL</th>
              <th class="px-3 py-2 text-center">BLK</th>
              <th class="px-3 py-2 text-center">TOV</th>
              <th class="px-3 py-2 text-center">PF</th>
              <th class="px-3 py-2 text-right">PTS</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#374151] text-[#F3F4F6]">
            @foreach($A['players'] as $p)
              <tr class="odd:bg-[#1f2937] even:bg-[#111827]">
                <td class="px-3 py-2">
                  <div class="flex items-center gap-2">
                    @if(!empty($p['img']))
                      <img src="{{ $p['img'] }}" class="h-6 w-6 rounded-full object-cover ring-1 ring-white/10" />
                    @else
                      <div class="h-6 w-6 rounded-full bg-white/10"></div>
                    @endif
                    <span class="whitespace-nowrap">{{ $p['name'] }}</span>
                  </div>
                </td>
                <td class="px-3 py-2 text-center">{{ $p['min'] ?? '—' }}</td>
                <td class="px-3 py-2 text-center">{{ $p['fg'] ?? '—' }}</td>
                <td class="px-3 py-2 text-center">{{ $p['tp'] ?? '—' }}</td>
                <td class="px-3 py-2 text-center">{{ $p['ft'] ?? '—' }}</td>
                <td class="px-3 py-2 text-center">{{ $p['reb'] ?? '—' }}</td>
                <td class="px-3 py-2 text-center">{{ $p['ast'] ?? '—' }}</td>
                <td class="px-3 py-2 text-center">{{ $p['stl'] ?? '—' }}</td>
                <td class="px-3 py-2 text-center">{{ $p['blk'] ?? '—' }}</td>
                <td class="px-3 py-2 text-center">{{ $p['tov'] ?? '—' }}</td>
                <td class="px-3 py-2 text-center">{{ $p['pf']  ?? '—' }}</td>
                <td class="px-3 py-2 text-right font-semibold">{{ $p['pts'] ?? '—' }}</td>
              </tr>
            @endforeach
            {{-- Totals row --}}
            <tr class="bg-[#0b1220] text-white font-medium">
              <td class="px-3 py-2">Totals</td>
              <td class="px-3 py-2 text-center">—</td>
              <td class="px-3 py-2 text-center">
                {{ $A['totals']['fg']['m'] ?? 0 }}-{{ $A['totals']['fg']['a'] ?? 0 }}
                @if(!is_null($A['totals']['fg']['pct'])) ({{ $A['totals']['fg']['pct'] }}%) @endif
              </td>
              <td class="px-3 py-2 text-center">
                {{ $A['totals']['tp']['m'] ?? 0 }}-{{ $A['totals']['tp']['a'] ?? 0 }}
                @if(!is_null($A['totals']['tp']['pct'])) ({{ $A['totals']['tp']['pct'] }}%) @endif
              </td>
              <td class="px-3 py-2 text-center">
                {{ $A['totals']['ft']['m'] ?? 0 }}-{{ $A['totals']['ft']['a'] ?? 0 }}
                @if(!is_null($A['totals']['ft']['pct'])) ({{ $A['totals']['ft']['pct'] }}%) @endif
              </td>
              <td class="px-3 py-2 text-center">{{ $A['totals']['reb'] ?? 0 }}</td>
              <td class="px-3 py-2 text-center">{{ $A['totals']['ast'] ?? 0 }}</td>
              <td class="px-3 py-2 text-center">{{ $A['totals']['stl'] ?? 0 }}</td>
              <td class="px-3 py-2 text-center">{{ $A['totals']['blk'] ?? 0 }}</td>
              <td class="px-3 py-2 text-center">{{ $A['totals']['tov'] ?? 0 }}</td>
              <td class="px-3 py-2 text-center">{{ $A['totals']['pf']  ?? 0 }}</td>
              <td class="px-3 py-2 text-right font-semibold">{{ $A['totals']['pts'] ?? 0 }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

    {{-- Team B table --}}
    <section class="bg-[#1f2937] border border-[#374151] rounded-xl overflow-hidden">
      <div class="px-4 py-3 bg-[#0f172a] border-b border-[#374151] flex items-center justify-between">
        <h2 class="font-semibold">{{ $B['team'] ?? 'Team B' }}</h2>
        <div class="text-sm text-gray-300">PTS: <span class="font-semibold text-white">{{ $B['totals']['pts'] ?? '—' }}</span></div>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-[900px] w-full text-sm">
          <thead class="bg-[#0f172a] text-gray-300">
            <tr>
              <th class="px-3 py-2 text-left">Player</th>
              <th class="px-3 py-2 text-center">MIN</th>
              <th class="px-3 py-2 text-center">FG</th>
              <th class="px-3 py-2 text-center">3PT</th>
              <th class="px-3 py-2 text-center">FT</th>
              <th class="px-3 py-2 text-center">REB</th>
              <th class="px-3 py-2 text-center">AST</th>
              <th class="px-3 py-2 text-center">STL</th>
              <th class="px-3 py-2 text-center">BLK</th>
              <th class="px-3 py-2 text-center">TOV</th>
              <th class="px-3 py-2 text-center">PF</th>
              <th class="px-3 py-2 text-right">PTS</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#374151] text-[#F3F4F6]">
            @foreach($B['players'] as $p)
              <tr class="odd:bg-[#1f2937] even:bg-[#111827]">
                <td class="px-3 py-2">
                  <div class="flex items-center gap-2">
                    @if(!empty($p['img']))
                      <img src="{{ $p['img'] }}" class="h-6 w-6 rounded-full object-cover ring-1 ring-white/10" />
                    @else
                      <div class="h-6 w-6 rounded-full bg-white/10"></div>
                    @endif
                    <span class="whitespace-nowrap">{{ $p['name'] }}</span>
                  </div>
                </td>
                <td class="px-3 py-2 text-center">{{ $p['min'] ?? '—' }}</td>
                <td class="px-3 py-2 text-center">{{ $p['fg'] ?? '—' }}</td>
                <td class="px-3 py-2 text-center">{{ $p['tp'] ?? '—' }}</td>
                <td class="px-3 py-2 text-center">{{ $p['ft'] ?? '—' }}</td>
                <td class="px-3 py-2 text-center">{{ $p['reb'] ?? '—' }}</td>
                <td class="px-3 py-2 text-center">{{ $p['ast'] ?? '—' }}</td>
                <td class="px-3 py-2 text-center">{{ $p['stl'] ?? '—' }}</td>
                <td class="px-3 py-2 text-center">{{ $p['blk'] ?? '—' }}</td>
                <td class="px-3 py-2 text-center">{{ $p['tov'] ?? '—' }}</td>
                <td class="px-3 py-2 text-center">{{ $p['pf']  ?? '—' }}</td>
                <td class="px-3 py-2 text-right font-semibold">{{ $p['pts'] ?? '—' }}</td>
              </tr>
            @endforeach
            {{-- Totals row --}}
            <tr class="bg-[#0b1220] text-white font-medium">
              <td class="px-3 py-2">Totals</td>
              <td class="px-3 py-2 text-center">—</td>
              <td class="px-3 py-2 text-center">
                {{ $B['totals']['fg']['m'] ?? 0 }}-{{ $B['totals']['fg']['a'] ?? 0 }}
                @if(!is_null($B['totals']['fg']['pct'])) ({{ $B['totals']['fg']['pct'] }}%) @endif
              </td>
              <td class="px-3 py-2 text-center">
                {{ $B['totals']['tp']['m'] ?? 0 }}-{{ $B['totals']['tp']['a'] ?? 0 }}
                @if(!is_null($B['totals']['tp']['pct'])) ({{ $B['totals']['tp']['pct'] }}%) @endif
              </td>
              <td class="px-3 py-2 text-center">
                {{ $B['totals']['ft']['m'] ?? 0 }}-{{ $B['totals']['ft']['a'] ?? 0 }}
                @if(!is_null($B['totals']['ft']['pct'])) ({{ $B['totals']['ft']['pct'] }}%) @endif
              </td>
              <td class="px-3 py-2 text-center">{{ $B['totals']['reb'] ?? 0 }}</td>
              <td class="px-3 py-2 text-center">{{ $B['totals']['ast'] ?? 0 }}</td>
              <td class="px-3 py-2 text-center">{{ $B['totals']['stl'] ?? 0 }}</td>
              <td class="px-3 py-2 text-center">{{ $B['totals']['blk'] ?? 0 }}</td>
              <td class="px-3 py-2 text-center">{{ $B['totals']['tov'] ?? 0 }}</td>
              <td class="px-3 py-2 text-center">{{ $B['totals']['pf']  ?? 0 }}</td>
              <td class="px-3 py-2 text-right font-semibold">{{ $B['totals']['pts'] ?? 0 }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

  </div>

</main>
@endsection
