<?php

namespace App\Http\Controllers;
use App\Models\NbaTeam;
use App\Models\NbaPlayer;
use App\Models\NbaGame;
use App\Models\NbaPlayerGamelog;
use App\Services\NbaService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\NbaStanding;
use Illuminate\Support\Facades\DB;

class NbaController extends Controller
{   
    protected NbaService $nba;

    public function __construct(NbaService $nba)
    {
        $this->nba = $nba;
    }

    public function home()
    {
        return view('nba.home');
    }

    public function allPlayers(Request $request)
{
    $page    = max((int) $request->query('page', 1), 1);
    $perPage = min(max((int) $request->query('perPage', 50), 10), 200);
    $q       = trim((string) $request->query('q', ''));
    $sort    = (string) $request->query('sort', 'name');
    $dir     = strtolower((string) $request->query('dir', 'asc')) === 'desc' ? 'desc' : 'asc';

    $query = NbaPlayer::query();

    // search
    if ($q !== '') {
        $query->where(function ($qBuilder) use ($q) {
            $qBuilder
                ->whereRaw('LOWER(full_name) LIKE ?', ['%' . strtolower($q) . '%'])
                ->orWhereRaw('LOWER(team_name) LIKE ?', ['%' . strtolower($q) . '%']);
        });
    }

    // sorting
    switch ($sort) {
        case 'team':
            $query->orderBy('team_name', $dir);
            break;
        case 'height':
            // assumes display_height stored as inches or plain string
            $query->orderByRaw('CAST(display_height as UNSIGNED) ' . $dir);
            break;
        case 'weight':
            $query->orderByRaw('CAST(display_weight as UNSIGNED) ' . $dir);
            break;
        case 'name':
        default:
            $query->orderBy('full_name', $dir);
            break;
    }

    $players = $query->paginate($perPage, ['*'], 'page', $page);

    return view('nba.players', ['players' => $players]);
}



public function showTeam($external_id)
{
    $team = NbaTeam::where('external_id', $external_id)->firstOrFail();

    $players = NbaPlayer::where('team_id', $external_id)
        ->orderBy('full_name')->get();

    $games = NbaGame::where('home_team_id', $external_id)
        ->orWhere('away_team_id', $external_id)
        ->orderBy('tipoff')->get();

    // Latest row (for your existing "current" stats block)
    $standing = NbaStanding::where('team_id', $external_id)
        ->orderByDesc('season')
        ->first();

    // History from 2021 through latest season
    $standingsHistory = NbaStanding::where('team_id', $external_id)
        ->where('season', '>=', 2021)
        ->orderByDesc('season')
        ->get();

    return view('nba.team_show', compact('team','players','games','standing','standingsHistory'));
}


public function showPlayer(Request $request, $external_id)
{
    $player = NbaPlayer::with([
            'gamelogs' => fn ($q) => $q->orderBy('game_date', 'desc'),
            'details'
        ])
        ->where('external_id', $external_id)
        ->firstOrFail();

    $details = $player->details;

    // ---- Build header team (id/name/logo) ----
    $headerTeam = null;
    if ($details && ($details->team_id ?? null)) {
        $headerTeam = NbaTeam::where('external_id', $details->team_id)->first();
    }
    if (!$headerTeam && ($player->team_id ?? null)) {
        $headerTeam = NbaTeam::where('external_id', $player->team_id)->first();
    }
    if (!$headerTeam && ($player->team_name ?? null)) {
        $headerTeam = NbaTeam::where('name', $player->team_name)
            ->orWhere('short_name', $player->team_name)
            ->orWhere('abbreviation', $player->team_name)
            ->first();
    }

    // ---- Opponent team resolution for gamelogs ----
    $teams = NbaTeam::select('external_id','name','short_name','abbreviation','logo')->get();
    $norm = fn (?string $s) => Str::of((string)$s)->lower()
        ->replace(['.', ',', '-', '–', '—', '\''], ' ')
        ->squish()
        ->toString();

    $byName  = $teams->keyBy(fn ($t) => $norm($t->name));
    $byShort = $teams->keyBy(fn ($t) => $norm($t->short_name));
    $byAbbr  = $teams->keyBy(fn ($t) => $norm($t->abbreviation));

    $gamelogs = $player->gamelogs->map(function ($log) use ($norm, $byName, $byShort, $byAbbr) {
        $key = $norm($log->opponent_name);
        $hit = $byName->get($key)
            ?? $byShort->get($key)
            ?? $byAbbr->get($key);

        if (!$hit && $key) {
            $hit = $byName->first(fn ($t) => Str::contains($key, $norm($t->name)))
                ?? $byShort->first(fn ($t) => Str::contains($key, $norm($t->short_name)))
                ?? $byAbbr->first(fn ($t) => Str::contains($key, $norm($t->abbreviation)));
        }

        if ($hit) {
            $log->setAttribute('opponent_team_id',   $hit->external_id);
            $log->setAttribute('opponent_team_name', $hit->name);
            $log->setAttribute('opponent_team_logo', $hit->logo);
        }

        return $log;
    });

    // ---- Status ----
    $cleanStatus = null;
    if ($details && !empty($details->status_name)) {
        $cleanStatus = $details->status_name;
    } elseif ($details && !is_null($details->active)) {
        $cleanStatus = $details->active ? 'Active' : 'Inactive';
    }

    // ---- Averages ----
    $career = $player->gamelogs()
        ->selectRaw('
            COUNT(*) as games,
            AVG(points) as pts,
            AVG(rebounds) as reb,
            AVG(assists) as ast,
            AVG(steals) as stl,
            AVG(blocks) as blk,
            AVG(minutes) as min
        ')
        ->first();

    $currentYear = now()->year;
    $season = $player->gamelogs()
        ->whereYear('game_date', $currentYear)
        ->selectRaw('
            COUNT(*) as games,
            AVG(points) as pts,
            AVG(rebounds) as reb,
            AVG(assists) as ast,
            AVG(steals) as stl,
            AVG(blocks) as blk,
            AVG(minutes) as min
        ')
        ->first();

    return view('nba.player_show', [
        'player'        => $player,
        'details'       => $details,
        'teamHeader'    => $headerTeam,
        'gamelogs'      => $gamelogs,
        'cleanStatus'   => $cleanStatus,
        'career'        => $career,
        'season'        => $season,
    ]);
}

public function upcomingGames()
{
    $games = NbaGame::query()
        ->where('tipoff', '>=', Carbon::now()) // only future games
        ->orderBy('tipoff')
        ->take(20)
        ->get();

    return view('nba.games', compact('games'));
}
public function allTeams(Request $request)
{
    $q = trim((string) $request->query('q', ''));

    $teams = NbaTeam::query()
        ->when($q !== '', function ($query) use ($q) {
            $query->where(fn($q2) => $q2
                ->where('name', 'like', "%{$q}%")
                ->orWhere('short_name', 'like', "%{$q}%")
                ->orWhere('abbreviation', 'like', "%{$q}%")
            );
        })
        ->orderBy('name')
        ->paginate(31)               // grid-friendly page size
        ->withQueryString();         // keep search term on next pages

    return view('nba.teams', compact('teams', 'q'));
}

    public function allGames()
    {
        $games = $this->nba->allGames();
        return view('nba.all_games', ['games' => $games['response'] ?? []]);
    }

    public function showGame($id)
    {
        $game = $this->nba->showGame($id);
        return view('nba.game_detail', ['game' => $game['response'][0] ?? null]);
    }



public function Explorer(Request $request)
{
    // Seasons list for filters
    $seasons = NbaStanding::query()
        ->select('season')
        ->distinct()
        ->orderBy('season', 'desc')
        ->pluck('season')
        ->toArray();

    $minSeason = count($seasons) ? min($seasons) : 2021;
    $maxSeason = count($seasons) ? max($seasons) : (int) date('Y');

    $from = (int) $request->input('from', $minSeason);
    $to   = (int) $request->input('to', $maxSeason);
    if ($from > $to) { [$from, $to] = [$to, $from]; }

    $teamQuery = trim((string) $request->input('team', ''));

    // Query + filters
    $q = NbaStanding::query()
        ->when($from, fn($qq) => $qq->where('season', '>=', $from))
        ->when($to,   fn($qq) => $qq->where('season', '<=', $to))
        ->when($teamQuery !== '', function ($qq) use ($teamQuery) {
            $qq->where(function ($sub) use ($teamQuery) {
                $sub->where('team_name', 'like', "%{$teamQuery}%")
                    ->orWhere('abbreviation', 'like', "%{$teamQuery}%");
            });
        })
        ->orderBy('season', 'desc')
        ->orderBy('team_name');

    $collection = $q->get();

    // Build a logo map: prefer DB logo, else fallback to ESPN by abbreviation
    $teamIds = $collection->pluck('team_id')->unique()->values();
    $teams   = NbaTeam::whereIn('external_id', $teamIds)->get(['external_id','abbreviation','logo','logo_dark']);

    $logoMap = [];
    foreach ($teams as $t) {
        $abbr = strtolower($t->abbreviation ?? '');
        $fallback = $abbr ? "https://a.espncdn.com/i/teamlogos/nba/500/{$abbr}.png" : null;
        $logoMap[$t->external_id] = $t->logo ?: $fallback;
    }

    // Map to clean rows for the view
    $rows = $collection->map(function (NbaStanding $r) use ($logoMap) {
        $winPercentDisp = $r->win_percent !== null? (string) round($r->win_percent * 100) . '%': '—';
        $ppgFmt        = $r->avg_points_for !== null ? number_format($r->avg_points_for, 1) : '—';
        $oppPpgFmt     = $r->avg_points_against !== null ? number_format($r->avg_points_against, 1) : '—';

        $diffTxt   = '—';
        $diffClass = 'text-gray-300';
        if ($r->point_differential !== null) {
            $diffTxt   = ($r->point_differential >= 0 ? '+' : '') . $r->point_differential;
            $diffClass = $r->point_differential >= 0 ? 'text-[#84CC16]' : 'text-[#F97316]';
        }

        $streakTxt = '—';
        if (is_int($r->streak)) {
            $streakTxt = $r->streak > 0 ? 'W' . $r->streak
                : ($r->streak < 0 ? 'L' . abs($r->streak) : '—');
        }

        $teamLogo = $logoMap[$r->team_id] ?? null;

        // This payload is used by the compare panel (include logo here!)
        $payload = json_encode([
            'season'      => $r->season,
            'team'        => $r->team_name,
            'abbr'        => $r->abbreviation,
            'logo'        => $teamLogo,
            'wins'        => $r->wins,
            'losses'      => $r->losses,
            'win_percent' => $r->win_percent,
            'seed'        => $r->playoff_seed,
            'gb'          => $r->games_behind,
            'ppg'         => $r->avg_points_for,
            'opp_ppg'     => $r->avg_points_against,
            'diff'        => $r->point_differential,
            'win_percent_fmt' => $winPercentDisp,
            'home'        => $r->home_record,
            'road'        => $r->road_record,
            'l10'         => $r->last_ten,
            'streak'      => $r->streak,
            'clincher'    => $r->clincher,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return [
            'team_id'            => $r->team_id,
            'team_name'          => $r->team_name,
            'abbreviation'       => $r->abbreviation,
            'season'             => $r->season,
            'wins'               => $r->wins,
            'losses'             => $r->losses,
            'win_percent'        => $r->win_percent,
            'playoff_seed'       => $r->playoff_seed,
            'games_behind'       => $r->games_behind,
            'avg_points_for'     => $r->avg_points_for,
            'avg_points_against' => $r->avg_points_against,
            'point_differential' => $r->point_differential,
            'home_record'        => $r->home_record,
            'road_record'        => $r->road_record,
            'last_ten'           => $r->last_ten,
            'streak'             => $r->streak,
            'clincher'           => $r->clincher,

            'data_team'          => strtolower(trim(($r->team_name ?? '') . ' ' . ($r->abbreviation ?? ''))),

            'win_percent_fmt'    => $winPercentDisp,
            'ppg_fmt'            => $ppgFmt,
            'opp_ppg_fmt'        => $oppPpgFmt,
            'diff_txt'           => $diffTxt,
            'diff_class'         => $diffClass,
            'streak_txt'         => $streakTxt,

            'team_logo'          => $teamLogo,
            'payload'            => $payload,
        ];
    })->values();

    // CSV export (unchanged)
    if ($request->boolean('export')) {
        $filename = "standings_{$from}_{$to}.csv";
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        $cols = [
            'season','team_name','abbreviation','wins','losses','win_percent','playoff_seed','games_behind',
            'avg_points_for','avg_points_against','point_differential','home_record','road_record','last_ten',
            'streak','clincher',
        ];

        return response()->streamDownload(function () use ($rows, $cols) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $cols);
            foreach ($rows as $r) {
                $line = [];
                foreach ($cols as $c) {
                    $line[] = $r[$c] ?? null;
                }
                fputcsv($out, $line);
            }
            fclose($out);
        }, $filename, $headers);
    }

    $legend = [
        ['Record',     'Wins–Losses for the season.'],
        ['Win%',       'Winning percentage (wins ÷ total games).'],
        ['Seed',       'Projected/Final playoff seed.'],
        ['GB',         'Games behind the conference/league leader.'],
        ['PPG',        'Average points scored per game.'],
        ['OPP PPG',    'Average points allowed per game.'],
        ['Diff',       'Points For − Points Against (total).'],
        ['Home/Road',  'Win–loss records at home and away.'],
        ['L10',        'Record in the last 10 games.'],
        ['Streak',     'Current win (W) or loss (L) streak length.'],
        ['Clincher',   'Markers like *, z, x for titles/berths.'],
    ];

    return view('nba.standings_explorer', [
        'seasons'    => $seasons,
        'from'       => $from,
        'to'         => $to,
        'teamQuery'  => $teamQuery,
        'rows'       => $rows,
        'legend'     => $legend,
    ]);
}



public function playersExplorer(Request $request)
{
    // Seasons (from logs’ game_date)
    $seasonRows = NbaPlayerGameLog::query()
        ->selectRaw('DISTINCT YEAR(game_date) AS season')
        ->orderByDesc('season')
        ->pluck('season')
        ->toArray();

    $minSeason = $seasonRows ? min($seasonRows) : 2021;
    $maxSeason = $seasonRows ? max($seasonRows) : (int) date('Y');

    $from = (int) $request->input('from', $minSeason);
    $to   = (int) $request->input('to', $maxSeason);
    if ($from > $to) { [$from, $to] = [$to, $from]; }

    $teamQuery   = trim((string) $request->input('team', ''));   // name or abbr.
    $playerQuery = trim((string) $request->input('player', '')); // player name

    // Per-season aggregates per player
    $agg = NbaPlayerGameLog::query()
        ->join('nba_players as p', 'p.external_id', '=', 'nba_player_game_logs.player_external_id')
        ->leftJoin('nba_player_details as d', 'd.external_id', '=', 'p.external_id')
        // 👇 NEW: get abbreviation/logo from teams
        ->leftJoin('nba_teams as t', 't.external_id', '=', 'p.team_id')
        ->selectRaw('
            YEAR(nba_player_game_logs.game_date) as season,
            p.external_id as player_id,
            COALESCE(p.full_name, CONCAT(p.first_name," ",p.last_name)) as player_name,
            p.team_id as team_id,
            p.team_name as team_name,

            t.abbreviation as team_abbr,      -- from teams
            p.team_logo as p_logo,            -- player row logo (if any)
            t.logo as t_logo,                 -- teams logo (fallback)

            COALESCE(d.headshot_href, p.image) as headshot,

            COUNT(*) as games,
            SUM(CASE WHEN UPPER(TRIM(result)) LIKE "W%" THEN 1 ELSE 0 END) as wins,
            SUM(CASE WHEN UPPER(TRIM(result)) LIKE "L%" THEN 1 ELSE 0 END) as losses,

            AVG(points)   as ppg,
            AVG(rebounds) as rpg,
            AVG(assists)  as apg,
            AVG(steals)   as spg,
            AVG(blocks)   as bpg,
            AVG(turnovers) as tpg,
            AVG(minutes)  as mpg,

            AVG(fg_pct)        as fg_pct,
            AVG(three_pt_pct)  as three_pt_pct,
            AVG(ft_pct)        as ft_pct
        ')
        ->when($from, fn($q) => $q->whereRaw('YEAR(nba_player_game_logs.game_date) >= ?', [$from]))
        ->when($to,   fn($q) => $q->whereRaw('YEAR(nba_player_game_logs.game_date) <= ?', [$to]))
        ->when($teamQuery !== '', function ($q) use ($teamQuery) {
            $like = "%{$teamQuery}%";
            // match by team name (players table) OR abbreviation (teams table)
            $q->where(function ($sub) use ($like) {
                $sub->where('p.team_name', 'like', $like)
                    ->orWhere('t.abbreviation', 'like', $like);
            });
        })
        ->when($playerQuery !== '', function ($q) use ($playerQuery) {
            $like = "%{$playerQuery}%";
            $q->where(function ($sub) use ($like) {
                $sub->where('p.full_name', 'like', $like)
                    ->orWhere('p.first_name', 'like', $like)
                    ->orWhere('p.last_name', 'like', $like);
            });
        })
        // Group by the raw columns we selected (avoid expressions)
        ->groupBy(
            'season','player_id','player_name',
            'team_id','team_name',
            'team_abbr','p_logo','t_logo','headshot'
        )
        ->orderByDesc('season')
        ->orderBy('player_name');

    $collection = $agg->get();

    // Percent formatter: scale 0–1 to %, leave 1–100 as-is.
    $percentFmt = function ($v) {
        if ($v === null) return '—';
        $n = (float)$v;
        return $n <= 1 ? number_format($n * 100, 1) . '%' : number_format($n, 1) . '%';
    };

    // Map to clean rows for the view (and compare payload)
    $rows = $collection->map(function ($r) use ($percentFmt) {
        $one = fn($v) => $v !== null ? number_format($v, 1) : '—';
        $logo = $r->p_logo ?: $r->t_logo; // prefer player row logo, fallback to teams.logo

        $payload = json_encode([
            'season'   => (int) $r->season,
            'player'   => $r->player_name,
            'player_id'=> (int) $r->player_id,
            'team'     => $r->team_name,
            'abbr'     => $r->team_abbr,
            'logo'     => $logo,
            'headshot' => $r->headshot,
            'games'    => (int) $r->games,
            'wins'     => (int) $r->wins,
            'losses'   => (int) $r->losses,

            'ppg'      => $r->ppg,
            'rpg'      => $r->rpg,
            'apg'      => $r->apg,
            'spg'      => $r->spg,
            'bpg'      => $r->bpg,
            'tpg'      => $r->tpg,
            'mpg'      => $r->mpg,
            'fg_pct'   => $r->fg_pct,
            'tp_pct'   => $r->three_pt_pct,
            'ft_pct'   => $r->ft_pct,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return [
            'season'     => (int) $r->season,
            'player_id'  => (int) $r->player_id,
            'player'     => $r->player_name,
            'team_id'    => (int) $r->team_id,
            'team'       => $r->team_name,
            'abbr'       => $r->team_abbr,
            'logo'       => $logo,
            'headshot'   => $r->headshot,

            'games'      => (int) $r->games,
            'wins'       => (int) $r->wins,
            'losses'     => (int) $r->losses,
            'wl_text'    => $r->wins.'–'.$r->losses,

            'ppg'        => $one($r->ppg),
            'rpg'        => $one($r->rpg),
            'apg'        => $one($r->apg),
            'spg'        => $one($r->spg),
            'bpg'        => $one($r->bpg),
            'tpg'        => $one($r->tpg),
            'mpg'        => $one($r->mpg),

            'fg_pct'     => $percentFmt($r->fg_pct),
            'tp_pct'     => $percentFmt($r->three_pt_pct),
            'ft_pct'     => $percentFmt($r->ft_pct),

            'data_text'  => strtolower(trim(($r->player_name ?? '').' '.$r->team_name.' '.($r->team_abbr ?? ''))),
            'payload'    => $payload,
        ];
    });

    // CSV export
    if ($request->boolean('export')) {
        $filename = "players_{$from}_{$to}.csv";
        $headers = ['Content-Type'=>'text/csv','Content-Disposition'=>"attachment; filename=\"{$filename}\""];
        $cols = [
            'season','player','team','abbr','games','wins','losses',
            'ppg','rpg','apg','spg','bpg','tpg','mpg','fg_pct','tp_pct','ft_pct',
        ];
        return response()->streamDownload(function () use ($rows, $cols) {
            $out = fopen('php://output','w'); fputcsv($out, $cols);
            foreach ($rows as $r) {
                $line = [];
                foreach ($cols as $c) { $line[] = $r[$c] ?? null; }
                fputcsv($out, $line);
            }
            fclose($out);
        }, $filename, $headers);
    }

    $seasons = array_values($seasonRows);

    $legend = [
        ['W/L','Team record in games the player appeared.'],
        ['PPG / RPG / APG','Points / Rebounds / Assists per game.'],
        ['SPG / BPG','Steals / Blocks per game.'],
        ['TOV','Turnovers per game (lower is better).'],
        ['MPG','Minutes per game.'],
        ['FG% / 3P% / FT%','Shooting percentages (averaged from logs).'],
    ];

    return view('nba.compare', [
        'seasons'     => $seasons,
        'from'        => $from,
        'to'          => $to,
        'teamQuery'   => $teamQuery,
        'playerQuery' => $playerQuery,
        'rows'        => $rows,
        'legend'      => $legend,
    ]);
}


}

