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

    $players   = NbaPlayer::where('team_id', $external_id)->get();
    $playerIds = $players->pluck('external_id');

    // Upcoming games table you already show
    $games = NbaGame::where('home_team_id', $external_id)
        ->orWhere('away_team_id', $external_id)
        ->orderBy('tipoff')
        ->get();

    // -------- Heuristic team-game selection from logs --------
    $ROSTER_EVENT_THRESHOLD = 5; // tune to 3–6 depending on data density

    // Events where >= N distinct current-roster players have a log for the same event
    $candidateEventIds = NbaPlayerGameLog::whereIn('player_external_id', $playerIds)
        ->select('event_id', DB::raw('COUNT(DISTINCT player_external_id) AS roster_hits'))
        ->groupBy('event_id')
        ->having('roster_hits', '>=', $ROSTER_EVENT_THRESHOLD)
        ->pluck('event_id');

    $useHeuristic = $candidateEventIds->count() > 0;

    if ($useHeuristic) {
        // Sum per event across all roster players
        $perEvent = NbaPlayerGameLog::whereIn('player_external_id', $playerIds)
            ->whereIn('event_id', $candidateEventIds)
            ->selectRaw('
                event_id,
                COALESCE(SUM(points), 0)   AS pts,
                COALESCE(SUM(rebounds), 0) AS reb,
                COALESCE(SUM(assists), 0)  AS ast,
                MAX(UPPER(TRIM(result)))   AS any_result,
                MIN(score)                 AS sample_score
            ')
            ->groupBy('event_id')
            ->get();

        $eventsCount = $perEvent->count();

        // Wins/Losses (normalize)
        $winLoss = $perEvent->reduce(function ($acc, $row) {
            $r = strtoupper((string) $row->any_result);
            if (Str::startsWith($r, 'W')) { $acc['wins']++; }
            elseif (Str::startsWith($r, 'L')) { $acc['losses']++; }
            return $acc;
        }, ['wins' => 0, 'losses' => 0]);

        // Totals
        $totals = [
            'pts' => (int) $perEvent->sum('pts'),
            'reb' => (int) $perEvent->sum('reb'),
            'ast' => (int) $perEvent->sum('ast'),
        ];

        // Averages
        $ppg = $eventsCount ? round($totals['pts'] / $eventsCount, 1) : 0.0;
        $rpg = $eventsCount ? round($totals['reb'] / $eventsCount, 1) : 0.0;
        $apg = $eventsCount ? round($totals['ast'] / $eventsCount, 1) : 0.0;

        $stats = (object)[
            'games'  => $eventsCount,
            'wins'   => $winLoss['wins'],
            'losses' => $winLoss['losses'],
            'ppg'    => $ppg,
            'rpg'    => $rpg,
            'apg'    => $apg,
            'source' => 'heuristic', // for optional debug
        ];
    } else {
        // ----- SAFETY FALLBACK (what you had before): logs-only -----
        $useLogs = NbaPlayerGameLog::whereIn('player_external_id', $playerIds);

        $eventsCount = (clone $useLogs)->distinct('event_id')->count('event_id');

        $tot = (clone $useLogs)->selectRaw('
                COALESCE(SUM(points), 0)   AS pts,
                COALESCE(SUM(rebounds), 0) AS reb,
                COALESCE(SUM(assists), 0)  AS ast
            ')->first();

        $perEventWL = (clone $useLogs)
            ->selectRaw("
                event_id,
                MAX(CASE WHEN UPPER(TRIM(result)) LIKE 'W%' THEN 1 ELSE 0 END) AS is_win,
                MAX(CASE WHEN UPPER(TRIM(result)) LIKE 'L%' THEN 1 ELSE 0 END) AS is_loss
            ")
            ->groupBy('event_id')
            ->get();

        $stats = (object)[
            'games'  => (int) $eventsCount,
            'wins'   => (int) $perEventWL->sum('is_win'),
            'losses' => (int) $perEventWL->sum('is_loss'),
            'ppg'    => $eventsCount ? round($tot->pts / $eventsCount, 1) : 0.0,
            'rpg'    => $eventsCount ? round($tot->reb / $eventsCount, 1) : 0.0,
            'apg'    => $eventsCount ? round($tot->ast / $eventsCount, 1) : 0.0,
            'source' => 'fallback',
        ];
    }

    return view('nba.team_show', compact('team','players','games','stats'));
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
}
