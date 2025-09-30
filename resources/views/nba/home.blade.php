@extends('layouts.nba')
@section('title','NBA — Home')

@section('content')
  {{-- HERO --}}
  <section
    class="relative -mt-16 w-full h-[60vh] sm:h-[70vh] lg:h-[80vh] bg-cover"
    style="background-image:url('{{ asset('storage/hero/3playerSplit_07d.webp') }}'); background-position:center 22%;"
  >
    <div class="absolute inset-0 bg-black/60"></div>
    <div class="relative z-10 max-w-7xl mx-auto h-full flex items-center px-4">
      <div class="space-y-6">
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white drop-shadow">
          NBA Hub
        </h1>
        <p class="text-[#F3F4F6]/90 max-w-xl">
          Players, teams, standings, schedules, and deep dive comparisons—built on your NBA data.
        </p>
        <div class="flex flex-wrap gap-3">
          <a href="{{ route('nba.players') }}"
             class="px-6 py-3 rounded-full bg-[#84CC16] text-[#111827] font-semibold hover:bg-[#a6e23a] transition">
            Explore Players
          </a>
          <a href="{{ route('nba.teams') }}"
             class="px-6 py-3 rounded-full bg-white/10 text-white border border-white/20 hover:bg-white/20 transition">
            Browse Teams
          </a>
        </div>
      </div>
    </div>
  </section>

  <div class="max-w-7xl mx-auto px-4 space-y-16 pt-10">

    {{-- QUICK NAV CARDS --}}
    <section>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <a href="{{ route('nba.games.upcoming') }}"
           class="group rounded-2xl p-6 bg-[#0f172a] border border-[#1f2937]/70 hover:border-[#84CC16] transition shadow">
          <div class="text-sm text-[#9CA3AF]">Games</div>
          <div class="mt-2 text-2xl font-bold text-white">Upcoming</div>
          <div class="mt-3 text-[#F3F4F6]/80">See tonight’s slate and what’s next.</div>
          <div class="mt-4 text-[#84CC16] font-semibold">Open →</div>
        </a>

        <a href="{{ route('nba.standings.explorer') }}"
           class="group rounded-2xl p-6 bg-[#0f172a] border border-[#1f2937]/70 hover:border-[#84CC16] transition shadow">
          <div class="text-sm text-[#9CA3AF]">Standings</div>
          <div class="mt-2 text-2xl font-bold text-white">Explorer</div>
          <div class="mt-3 text-[#F3F4F6]/80">Compare teams across seasons and metrics.</div>
          <div class="mt-4 text-[#84CC16] font-semibold">Open →</div>
        </a>

        <a href="{{ route('nba.compare') }}"
           class="group rounded-2xl p-6 bg-[#0f172a] border border-[#1f2937]/70 hover:border-[#84CC16] transition shadow">
          <div class="text-sm text-[#9CA3AF]">Players</div>
          <div class="mt-2 text-2xl font-bold text-white">Compare</div>
          <div class="mt-3 text-[#F3F4F6]/80">Side-by-side season summaries.</div>
          <div class="mt-4 text-[#84CC16] font-semibold">Open →</div>
        </a>

        <a href="{{ route('nba.teams') }}"
           class="group rounded-2xl p-6 bg-[#0f172a] border border-[#1f2937]/70 hover:border-[#84CC16] transition shadow">
          <div class="text-sm text-[#9CA3AF]">Teams</div>
          <div class="mt-2 text-2xl font-bold text-white">Directory</div>
          <div class="mt-3 text-[#F3F4F6]/80">Logos, rosters, and schedule.</div>
          <div class="mt-4 text-[#84CC16] font-semibold">Open →</div>
        </a>
      </div>
    </section>

    {{-- UPCOMING GAMES --}}
    @if($upcomingGames->isNotEmpty())
      <section>
        <h2 class="text-2xl font-bold text-white mb-4">Upcoming Games</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          @foreach($upcomingGames as $g)
            <article class="bg-[#0f172a] border border-[#1f2937]/70 rounded-2xl p-5 shadow hover:shadow-xl transition">
              <div class="text-sm text-[#9CA3AF]">
                {{ \Carbon\Carbon::parse($g->tipoff)->format('M d, Y H:i') }}
              </div>

              <div class="mt-3 flex items-center justify-between gap-3">
                {{-- Home --}}
                <div class="flex items-center gap-2 min-w-0">
                  <div class="h-8 w-8 rounded bg-white grid place-items-center overflow-hidden">
                    @if($g->home_team_logo)
                      <img src="{{ $g->home_team_logo }}" class="h-full w-full object-contain" alt="">
                    @endif
                  </div>
                  <div class="truncate">
                    {{ $g->home_team_name ?? $g->home_team_short ?? 'Home' }}
                  </div>
                </div>

                <div class="text-[#9CA3AF]">vs</div>

                {{-- Away --}}
                <div class="flex items-center gap-2 min-w-0 justify-end">
                  <div class="h-8 w-8 rounded bg-white grid place-items-center overflow-hidden">
                    @if($g->away_team_logo)
                      <img src="{{ $g->away_team_logo }}" class="h-full w-full object-contain" alt="">
                    @endif
                  </div>
                  <div class="truncate text-right">
                    {{ $g->away_team_name ?? $g->away_team_short ?? 'Away' }}
                  </div>
                </div>
              </div>

              <a href="{{ route('nba.games.show', $g->external_id ?? $g->id) }}"
                 class="mt-4 inline-flex items-center justify-center w-full px-4 py-2 rounded-lg bg-[#84CC16] text-[#111827] font-semibold hover:bg-[#a3e635] transition">
                Game details
              </a>
            </article>
          @endforeach
        </div>
      </section>
    @endif

    {{-- TOP SCORERS THIS YEAR --}}
    @if($topPpg->isNotEmpty())
      <section>
        <h2 class="text-2xl font-bold text-white mb-4">Top Scorers ({{ date('Y') }})</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          @foreach($topPpg as $row)
            <div class="bg-[#0f172a] border border-[#1f2937]/70 rounded-2xl p-5 shadow">
              <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-full bg-white/10 border border-white/10 grid place-items-center overflow-hidden">
                  @if($row->player_photo)
                    <img src="{{ $row->player_photo }}" class="h-full w-full object-cover" alt="">
                  @else
                    <div class="h-2 w-2 rounded-full bg-white/30"></div>
                  @endif
                </div>
                <div>
                  <div class="text-white font-semibold">
                    {{ $row->player_name ?? 'Unknown player' }}
                  </div>
                  <div class="text-xs text-[#9CA3AF]">{{ $row->g }} games</div>
                </div>
              </div>

              <div class="mt-4 text-3xl font-extrabold text-[#84CC16]">
                {{ number_format($row->ppg,1) }}
              </div>
              <div class="text-xs text-[#9CA3AF] -mt-1">PPG</div>
            </div>
          @endforeach
        </div>
      </section>
    @endif

    {{-- STANDINGS SNAPSHOT --}}
    @if($standings->isNotEmpty())
      <section>
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-2xl font-bold text-white">Standings Snapshot ({{ $latestSeason }})</h2>
          <a href="{{ route('nba.standings.explorer') }}" class="text-[#84CC16] font-medium hover:underline">
            Open Explorer →
          </a>
        </div>
        <div class="overflow-x-auto rounded-2xl border border-[#1f2937]/70 shadow">
          <table class="min-w-[640px] w-full">
            <thead class="bg-[#0f172a] text-[#F3F4F6]/70 text-xs uppercase">
              <tr>
                <th class="px-4 py-3 text-left">Team</th>
                <th class="px-4 py-3 text-right">W</th>
                <th class="px-4 py-3 text-right">L</th>
                <th class="px-4 py-3 text-right">Win%</th>
                <th class="px-4 py-3 text-right">PPG</th>
                <th class="px-4 py-3 text-right">OPP PPG</th>
                <th class="px-4 py-3 text-right">Diff</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#1f2937] bg-[#0b1220]">
              @foreach($standings as $s)
                <tr class="hover:bg-[#0f172a] transition">
                  <td class="px-4 py-3">{{ $s->team_name }}</td>
                  <td class="px-4 py-3 text-right tabular-nums">{{ $s->wins }}</td>
                  <td class="px-4 py-3 text-right tabular-nums">{{ $s->losses }}</td>
                  <td class="px-4 py-3 text-right tabular-nums">{{ $s->win_percent !== null ? number_format($s->win_percent,3) : '—' }}</td>
                  <td class="px-4 py-3 text-right tabular-nums">{{ $s->avg_points_for !== null ? number_format($s->avg_points_for,1) : '—' }}</td>
                  <td class="px-4 py-3 text-right tabular-nums">{{ $s->avg_points_against !== null ? number_format($s->avg_points_against,1) : '—' }}</td>
                  <td class="px-4 py-3 text-right tabular-nums {{ ($s->point_differential ?? 0) >= 0 ? 'text-[#84CC16]' : 'text-[#F97316]' }}">
                    {{ $s->point_differential >= 0 ? '+' : '' }}{{ $s->point_differential ?? '—' }}
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </section>
    @endif

    {{-- FEATURED TEAMS --}}
    @if($teams->isNotEmpty())
      <section>
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-2xl font-bold text-white">Featured Teams</h2>
          <a href="{{ route('nba.teams') }}" class="text-[#84CC16] font-medium hover:underline">See all →</a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-5">
          @foreach($teams as $t)
            <a href="{{ route('nba.team.show', $t->external_id) }}"
               class="group rounded-2xl p-4 bg-[#0f172a] border border-[#1f2937]/70 hover:border-[#84CC16] transition text-center shadow">
              <div class="h-20 w-full grid place-items-center rounded-xl bg-white overflow-hidden p-3">
                @if($t->logo)
                  <img src="{{ $t->logo }}" class="max-h-full max-w-full object-contain" alt="">
                @endif
              </div>
              <div class="mt-3 text-sm font-semibold text-white group-hover:text-[#84CC16] truncate">
                {{ $t->name }}
              </div>
            </a>
          @endforeach
        </div>
      </section>
    @endif

    <footer class="py-10 text-center text-sm text-[#F3F4F6]/60">
      &copy; {{ date('Y') }} NBA Hub.
    </footer>
  </div>
@endsection
