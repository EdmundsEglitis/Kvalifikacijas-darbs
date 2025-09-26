<!DOCTYPE html>
<html lang="lv">
<head>
  <meta charset="UTF-8">
  <title>{{ $team->name }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="antialiased text-[#F3F4F6] bg-[#111827]">

  {{-- NAVBAR --}}
  <nav class="fixed inset-x-0 top-0 z-50 bg-[#111827]/90 backdrop-blur-md border-b border-[#1f2937]">
    <div class="max-w-7xl mx-auto h-16 px-4 sm:px-6 lg:px-8 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <a href="{{ route('home') }}" class="shrink-0">
          <img src="{{ asset('home-icon-silhouette-svgrepo-com.svg') }}" alt="Home" class="h-8 w-8 filter invert"/>
        </a>
        <a href="{{ route('nba.home') }}" class="text-white font-bold hover:text-[#84CC16]">NBA</a>
      </div>

      {{-- Collapsible on mobile (simple) --}}
      <div class="hidden sm:flex items-center gap-5 text-sm font-medium">
        <a href="{{ route('nba.players') }}" class="hover:text-[#84CC16]">Players</a>
        <a href="{{ route('nba.games.upcoming') }}" class="hover:text-[#84CC16]">Upcoming Games</a>
        <a href="{{ route('nba.games.all') }}" class="hover:text-[#84CC16]">All Games</a>
        <a href="{{ route('nba.teams') }}" class="hover:text-[#84CC16]">Teams</a>
        <a href="{{ route('nba.stats') }}" class="hover:text-[#84CC16]">Stats</a>
      </div>
    </div>
  </nav>

  <main class="pt-24 max-w-7xl mx-auto px-4 space-y-10">

    {{-- TEAM HEADER --}}
    <section class="bg-[#1f2937] rounded-xl p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center gap-4">
      <div class="flex items-center gap-4">
        @if($team->logo)
          <img src="{{ $team->logo }}" alt="{{ $team->name }}" class="h-16 w-16 sm:h-20 sm:w-20 object-contain" loading="lazy">
        @else
          <div class="h-16 w-16 sm:h-20 sm:w-20 bg-[#0b1220] rounded grid place-items-center text-xs text-gray-400">No Logo</div>
        @endif

        <div>
          <h1 class="text-2xl sm:text-3xl font-bold text-white leading-tight">
            {{ $team->name }}
          </h1>
          <p class="text-gray-400">Abbreviation: {{ $team->abbreviation ?? '—' }}</p>
        </div>
      </div>

      @if($team->url)
        <div class="sm:ml-auto">
          <a href="{{ $team->url }}" target="_blank"
             class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-[#84CC16] text-[#111827] font-semibold hover:bg-[#a3e635] transition">
            View on ESPN
          </a>
        </div>
      @endif
    </section>

    {{-- ROSTER --}}
    <section>
      <h2 class="text-xl sm:text-2xl font-semibold mb-4 text-white">Roster</h2>

      {{-- Auto-fit grid for perfect responsiveness --}}
      <div class="grid gap-4 sm:gap-5 [grid-template-columns:repeat(auto-fit,minmax(150px,1fr))]">
        @forelse($players as $player)
          <a href="{{ route('nba.player.show', $player->external_id) }}"
             class="bg-[#1f2937] rounded-xl p-4 flex flex-col items-center hover:bg-[#374151] transition">
            @if($player->image)
              <img src="{{ $player->image }}" alt="{{ $player->full_name }}"
                   class="h-16 w-16 rounded-full mb-2 object-cover ring-2 ring-[#84CC16]" loading="lazy">
            @else
              <div class="h-16 w-16 rounded-full mb-2 grid place-items-center bg-gray-700 text-gray-400 text-xs">No Photo</div>
            @endif
            <h3 class="text-sm font-semibold text-gray-200 text-center line-clamp-2">
              {{ $player->full_name }}
            </h3>
          </a>
        @empty
          <p class="text-gray-400">No players found.</p>
        @endforelse
      </div>
    </section>

    {{-- UPCOMING GAMES --}}
    <section>
      <h2 class="text-xl sm:text-2xl font-semibold mb-4 text-white">Upcoming Games</h2>
      <div class="overflow-x-auto bg-[#1f2937] rounded-xl border border-[#374151]">
        <table class="min-w-full text-left text-sm">
          <thead class="bg-[#0f172a] text-gray-400">
            <tr>
              <th class="px-4 py-2">Date</th>
              <th class="px-4 py-2">Home</th>
              <th class="px-4 py-2">Away</th>
              <th class="px-4 py-2">Venue</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#374151] text-[#F3F4F6]">
            @forelse($games as $game)
              <tr class="odd:bg-[#1f2937] even:bg-[#111827] hover:bg-[#374151] transition">
                <td class="px-4 py-2 whitespace-nowrap">
                  {{ $game->tipoff ? \Carbon\Carbon::parse($game->tipoff)->format('M d, H:i') : '—' }}
                </td>
                <td class="px-4 py-2">
                  <a href="{{ route('nba.team.show', $game->home_team_id) }}"
                     class="flex items-center gap-2 hover:text-[#84CC16]">
                    @if($game->home_team_logo)
                      <img src="{{ $game->home_team_logo }}" class="h-6 w-6" loading="lazy">
                    @endif
                    <span class="truncate">{{ $game->home_team_name }}</span>
                  </a>
                </td>
                <td class="px-4 py-2">
                  <a href="{{ route('nba.team.show', $game->away_team_id) }}"
                     class="flex items-center gap-2 hover:text-[#84CC16]">
                    @if($game->away_team_logo)
                      <img src="{{ $game->away_team_logo }}" class="h-6 w-6" loading="lazy">
                    @endif
                    <span class="truncate">{{ $game->away_team_name }}</span>
                  </a>
                </td>
                <td class="px-4 py-2">
                  <span class="block truncate">{{ $game->venue }} @if($game->city) — {{ $game->city }} @endif</span>
                </td>
              </tr>
            @empty
              <tr><td colspan="4" class="px-4 py-3 text-gray-400">No upcoming games</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <p class="mt-2 text-xs text-gray-400 sm:hidden">Tip: you can scroll this table sideways on mobile.</p>
    </section>

    {{-- SEASON CARDS (2021 → now) --}}
    <section>
      <h2 class="text-xl sm:text-2xl font-semibold mb-4 text-white">Seasons (2021 →)</h2>

      @if($standingsHistory->isEmpty())
        <div class="bg-[#1f2937] rounded-xl p-6 text-gray-400">
          No standings history found.
        </div>
      @else
        <div class="grid gap-5 [grid-template-columns:repeat(auto-fit,minmax(260px,1fr))]">
          @foreach($standingsHistory as $row)
            @php
              $record = ($row->wins ?? 0).'–'.($row->losses ?? 0);
              $winPct = $row->win_percent !== null ? number_format($row->win_percent, 3) : '—';
              $ppg    = $row->avg_points_for !== null ? number_format($row->avg_points_for, 1) : '—';
              $opp    = $row->avg_points_against !== null ? number_format($row->avg_points_against, 1) : '—';
              $diff   = $row->point_differential;
              $diffTxt = $diff === null ? '—' : ($diff >= 0 ? "+$diff" : (string)$diff);
              $diffClass = $diff === null ? 'text-gray-300' : ($diff >= 0 ? 'text-[#84CC16]' : 'text-[#F97316]');
              $seed  = $row->playoff_seed ?? '—';
              $gb    = $row->games_behind ?? '—';
              $home  = $row->home_record ?? '—';
              $road  = $row->road_record ?? '—';
              $l10   = $row->last_ten ?? '—';
              $clin  = $row->clincher; // e.g. *, z, x (can be null)
              $streakBadge = null;
              if (is_int($row->streak)) {
                $streakBadge = $row->streak > 0 ? "W{$row->streak}" : ($row->streak < 0 ? "L".abs($row->streak) : "—");
              }
            @endphp

            <article class="group bg-[#1f2937] border border-[#374151] rounded-2xl shadow transition hover:-translate-y-0.5 hover:shadow-xl p-5">
              {{-- Header --}}
              <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                  <span class="inline-flex items-center gap-2">
                    @if(!empty($team->logo))
                      <img src="{{ $team->logo }}" class="h-5 w-5 object-contain" alt="{{ $team->name }}" loading="lazy">
                    @endif
                    <span class="text-white font-semibold">{{ $row->season }}</span>
                  </span>
                  @if($clin)
                    <span class="ml-2 text-[10px] px-2 py-0.5 rounded-full bg-white/10 text-white uppercase tracking-wide">
                      {{ $clin }}
                    </span>
                  @endif
                </div>

                @if($streakBadge)
                  <span class="text-[10px] px-2 py-0.5 rounded-full
                               {{ str_starts_with($streakBadge,'W') ? 'bg-[#84CC16]/20 text-[#84CC16]' : 'bg-[#F97316]/20 text-[#F97316]' }}">
                    {{ $streakBadge }}
                  </span>
                @endif
              </div>

              {{-- Record row --}}
              <div class="flex items-end justify-between mb-4 gap-3">
                <div>
                  <div class="text-xs text-[#F3F4F6]/70">Record</div>
                  <div class="text-2xl font-extrabold text-white">{{ $record }}</div>
                  <div class="text-xs text-[#F3F4F6]/60">Win%: {{ $winPct }}</div>
                </div>
                <div class="text-right">
                  <div class="text-xs text-[#F3F4F6]/70">Seed</div>
                  <div class="text-xl font-bold text-white">{{ $seed }}</div>
                  <div class="text-xs text-[#F3F4F6]/60">GB: {{ $gb }}</div>
                </div>
              </div>

              {{-- Stat grid --}}
              <dl class="grid grid-cols-3 gap-3">
                <div class="rounded-xl bg-[#0f172a]/40 border border-[#374151] p-3 text-center">
                  <dt class="text-[11px] text-[#F3F4F6]/60">PPG</dt>
                  <dd class="text-lg font-bold text-white">{{ $ppg }}</dd>
                </div>
                <div class="rounded-xl bg-[#0f172a]/40 border border-[#374151] p-3 text-center">
                  <dt class="text-[11px] text-[#F3F4F6]/60">OPP PPG</dt>
                  <dd class="text-lg font-bold text-white">{{ $opp }}</dd>
                </div>
                <div class="rounded-xl bg-[#0f172a]/40 border border-[#374151] p-3 text-center">
                  <dt class="text-[11px] text-[#F3F4F6]/60">Diff</dt>
                  <dd class="text-lg font-bold {{ $diffClass }}">{{ $diffTxt }}</dd>
                </div>
              </dl>

              {{-- Footer chips --}}
              <div class="mt-4 flex flex-wrap gap-2 text-[11px] sm:text-xs">
                <span class="px-2.5 py-1 rounded-full bg-white/5 text-[#F3F4F6]/80 border border-white/10">Home: {{ $home }}</span>
                <span class="px-2.5 py-1 rounded-full bg-white/5 text-[#F3F4F6]/80 border border-white/10">Road: {{ $road }}</span>
                <span class="px-2.5 py-1 rounded-full bg-white/5 text-[#F3F4F6]/80 border border-white/10">L10: {{ $l10 }}</span>
              </div>
            </article>
          @endforeach
        </div>
      @endif
    </section>

    {{-- STAT EXPLANATIONS (legend) --}}
    <section class="pb-10">
      <h2 class="text-xl sm:text-2xl font-semibold mb-4 text-white">Stat explanations</h2>

      {{-- Grid of small “legend” cards, responsive auto-fit --}}
      <div class="grid gap-3 sm:gap-4 [grid-template-columns:repeat(auto-fit,minmax(180px,1fr))]">
        <div class="bg-[#1f2937] border border-[#374151] rounded-xl p-3">
          <div class="text-sm font-semibold text-white mb-1">Record</div>
          <p class="text-xs text-gray-300">Wins–Losses for the season.</p>
        </div>
        <div class="bg-[#1f2937] border border-[#374151] rounded-xl p-3">
          <div class="text-sm font-semibold text-white mb-1">Win%</div>
          <p class="text-xs text-gray-300">Winning percentage (wins ÷ total games).</p>
        </div>
        <div class="bg-[#1f2937] border border-[#374151] rounded-xl p-3">
          <div class="text-sm font-semibold text-white mb-1">Seed</div>
          <p class="text-xs text-gray-300">Projected/Final playoff seed.</p>
        </div>
        <div class="bg-[#1f2937] border border-[#374151] rounded-xl p-3">
          <div class="text-sm font-semibold text-white mb-1">GB</div>
          <p class="text-xs text-gray-300">Games behind the conference/league leader.</p>
        </div>
        <div class="bg-[#1f2937] border border-[#374151] rounded-xl p-3">
          <div class="text-sm font-semibold text-white mb-1">PPG</div>
          <p class="text-xs text-gray-300">Average points scored per game.</p>
        </div>
        <div class="bg-[#1f2937] border border-[#374151] rounded-xl p-3">
          <div class="text-sm font-semibold text-white mb-1">OPP PPG</div>
          <p class="text-xs text-gray-300">Average points allowed per game.</p>
        </div>
        <div class="bg-[#1f2937] border border-[#374151] rounded-xl p-3">
          <div class="text-sm font-semibold text-white mb-1">Diff</div>
          <p class="text-xs text-gray-300">Point differential (Points For − Points Against).</p>
        </div>
        <div class="bg-[#1f2937] border border-[#374151] rounded-xl p-3">
          <div class="text-sm font-semibold text-white mb-1">Home / Road</div>
          <p class="text-xs text-gray-300">Win–loss records in home and away games.</p>
        </div>
        <div class="bg-[#1f2937] border border-[#374151] rounded-xl p-3">
          <div class="text-sm font-semibold text-white mb-1">L10</div>
          <p class="text-xs text-gray-300">Win–loss record across the last 10 games.</p>
        </div>
        <div class="bg-[#1f2937] border border-[#374151] rounded-xl p-3">
          <div class="text-sm font-semibold text-white mb-1">Streak</div>
          <p class="text-xs text-gray-300">Current win (W) or loss (L) streak length.</p>
        </div>
      </div>
    </section>

  </main>
</body>
</html>
