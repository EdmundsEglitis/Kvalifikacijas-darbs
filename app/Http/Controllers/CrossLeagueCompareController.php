<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\NbaStanding;
use App\Models\NbaTeam;
class CrossLeagueCompareController extends Controller
{
    public function explorer(Request $request)
    {
        // ---------- Query params ----------
        $q        = trim((string) $request->query('q', ''));
        $from     = (int) $request->query('from', 0);
        $to       = (int) $request->query('to', 0);
        $nbaPer   = min(max((int) $request->query('nba_per', 25), 10), 200);
        $lbsPer   = min(max((int) $request->query('lbs_per', 25), 10), 200);
        $nbaPage  = max((int) $request->query('nba_page', 1), 1);
        $lbsPage  = max((int) $request->query('lbs_page', 1), 1);

        // ---------- Seasons (union across sources) ----------
        $nbaSeasons = DB::table('nba_player_game_logs')
            ->whereNotNull('game_date')
            ->selectRaw('DISTINCT YEAR(game_date) AS y')
            ->pluck('y')->toArray();

        $lbsSeasons = DB::table('games')
            ->whereNotNull('date')
            ->selectRaw('DISTINCT YEAR(date) AS y')
            ->pluck('y')->toArray();

        $seasons = collect(array_unique(array_merge($nbaSeasons, $lbsSeasons)))
            ->sortDesc()->values();

        $latest = (int) ($seasons->first() ?? date('Y'));
        if (!$from) $from = $latest;
        if (!$to)   $to   = $latest;
        if ($from > $to) { [$from, $to] = [$to, $from]; }

        // =========================================================
        // NBA: per-season aggregates per player + SEARCH + PAGING
        // =========================================================
        $nbaBase = DB::table('nba_player_game_logs as l')
            ->join('nba_players as p', 'p.external_id', '=', 'l.player_external_id')
            ->whereNotNull('l.game_date')
            ->whereBetween(DB::raw('YEAR(l.game_date)'), [$from, $to]);

        if ($q !== '') {
            $qLike = '%' . strtolower($q) . '%';
            $nbaBase->where(function ($w) use ($qLike) {
                $w->whereRaw('LOWER(CONCAT(p.first_name," ",p.last_name)) LIKE ?', [$qLike])
                  ->orWhereRaw('LOWER(p.team_name) LIKE ?', [$qLike]);
            });
        }

        $nbaAgg = $nbaBase
            ->selectRaw("
                p.external_id                        AS player_id,
                CONCAT(p.first_name,' ',p.last_name) AS player_name,
                p.image                              AS player_photo,
                p.team_id                            AS team_id,
                p.team_name                          AS team_name,
                p.team_logo                          AS team_logo,
                YEAR(l.game_date)                    AS season,

                COUNT(*)                             AS g,
                SUM(CASE WHEN l.result='W' THEN 1 ELSE 0 END) AS wins,

                SUM(COALESCE(l.points,0))    AS pts,
                SUM(COALESCE(l.rebounds,0))  AS reb,
                SUM(COALESCE(l.assists,0))   AS ast,
                SUM(COALESCE(l.steals,0))    AS stl,
                SUM(COALESCE(l.blocks,0))    AS blk,
                SUM(COALESCE(l.turnovers,0)) AS tov,

                /* from 'x-y' strings */
                SUM(CAST(SUBSTRING_INDEX(COALESCE(l.fg,'0-0'), '-', 1) AS UNSIGNED))  AS fgm,
                SUM(CAST(SUBSTRING_INDEX(COALESCE(l.fg,'0-0'), '-', -1) AS UNSIGNED)) AS fga,
                SUM(CAST(SUBSTRING_INDEX(COALESCE(l.three_pt,'0-0'), '-', 1) AS UNSIGNED))  AS tpm,
                SUM(CAST(SUBSTRING_INDEX(COALESCE(l.three_pt,'0-0'), '-', -1) AS UNSIGNED)) AS tpa,
                SUM(CAST(SUBSTRING_INDEX(COALESCE(l.ft,'0-0'), '-', 1) AS UNSIGNED))  AS ftm,
                SUM(CAST(SUBSTRING_INDEX(COALESCE(l.ft,'0-0'), '-', -1) AS UNSIGNED)) AS fta
            ")
            ->groupBy('player_id','player_name','player_photo','team_id','team_name','team_logo','season')
            ->orderByDesc('season')->orderBy('player_name');

        $nbaAll   = $nbaAgg->get();
        $nbaTotal = $nbaAll->count();
        $nbaSlice = $nbaAll->slice(($nbaPage-1)*$nbaPer, $nbaPer)->values();

        $nba = $nbaSlice->map(function ($r) {
            $g = max((int)$r->g, 0);
            $ppg = $g ? $r->pts / $g : null;
            $rpg = $g ? $r->reb / $g : null;
            $apg = $g ? $r->ast / $g : null;
            $spg = $g ? $r->stl / $g : null;
            $bpg = $g ? $r->blk / $g : null;
            $tpg = $g ? $r->tov / $g : null;

            $fg_pct = ($r->fga ?? 0) > 0 ? $r->fgm / $r->fga : null;
            $tp_pct = ($r->tpa ?? 0) > 0 ? $r->tpm / $r->tpa : null;
            $ft_pct = ($r->fta ?? 0) > 0 ? $r->ftm / $r->fta : null;

            $fmt = fn($v,$p=false)=>$v===null?'—':($p?number_format($v*100,1).'%' : number_format($v,1));

            return (object)[
                'season'      => (int)$r->season,
                'player_id'   => (int)$r->player_id,
                'team_id'     => (int)$r->team_id,
                'player_name' => $r->player_name,
                'headshot'    => $r->player_photo,
                'team_name'   => $r->team_name,
                'team_logo'   => $r->team_logo,
                'g'           => (int)$r->g,
                'wins'        => (int)$r->wins,
                'ppg'         => $fmt($ppg),
                'rpg'         => $fmt($rpg),
                'apg'         => $fmt($apg),
                'spg'         => $fmt($spg),
                'bpg'         => $fmt($bpg),
                'tpg'         => $fmt($tpg),
                'fg_pct'      => $fmt($fg_pct,true),
                'tp_pct'      => $fmt($tp_pct,true),
                'ft_pct'      => $fmt($ft_pct,true),

                // raw values for payload
                '_raw_ppg' => $ppg, '_raw_rpg'=>$rpg, '_raw_apg'=>$apg, '_raw_spg'=>$spg,
                '_raw_bpg' => $bpg, '_raw_tpg'=>$tpg, '_raw_fg'=>$fg_pct, '_raw_tp'=>$tp_pct, '_raw_ft'=>$ft_pct
            ];
        });

        // =========================================================
        // LBS: per-season aggregates per player + SEARCH + PAGING
        // =========================================================
        $lbsBase = DB::table('player_game_stats as pgs')
            ->join('games as g', 'g.id', '=', 'pgs.game_id')
            ->join('players as p', 'p.id', '=', 'pgs.player_id')
            ->join('teams as t', 't.id', '=', 'pgs.team_id')
            ->whereBetween(DB::raw('YEAR(g.date)'), [$from, $to]);

        if ($q !== '') {
            $qLike = '%' . strtolower($q) . '%';
            $lbsBase->where(function ($w) use ($qLike) {
                $w->whereRaw('LOWER(p.name) LIKE ?', [$qLike])
                  ->orWhereRaw('LOWER(t.name) LIKE ?', [$qLike]);
            });
        }

        $lbsAgg = $lbsBase
            ->selectRaw("
                p.id AS player_id, p.name AS player_name, p.photo AS player_photo,
                t.id AS team_id, t.name AS team_name, t.logo AS team_logo,
                YEAR(g.date) AS season,
                COUNT(*) AS g,
                SUM(CASE WHEN g.winner_id = pgs.team_id THEN 1 ELSE 0 END) AS wins,

                SUM(pgs.points) AS pts,
                SUM(pgs.reb)    AS reb,
                SUM(pgs.ast)    AS ast,
                SUM(pgs.stl)    AS stl,
                SUM(pgs.blk)    AS blk,
                SUM(pgs.tov)    AS tov,

                SUM(pgs.fgm2 + pgs.fgm3) AS fgm,
                SUM(pgs.fga2 + pgs.fga3) AS fga,
                SUM(pgs.fgm3)            AS tpm,
                SUM(pgs.fga3)            AS tpa,
                SUM(pgs.ftm)             AS ftm,
                SUM(pgs.fta)             AS fta
            ")
            ->groupBy('player_id','player_name','player_photo','team_id','team_name','team_logo','season')
            ->orderByDesc('season')->orderBy('player_name');

        $lbsAll   = $lbsAgg->get();
        $lbsTotal = $lbsAll->count();
        $lbsSlice = $lbsAll->slice(($lbsPage-1)*$lbsPer, $lbsPer)->values();

        $lbs = $lbsSlice->map(function ($r) {
            $g = max((int)$r->g, 0);
            $ppg = $g ? $r->pts / $g : null;
            $rpg = $g ? $r->reb / $g : null;
            $apg = $g ? $r->ast / $g : null;
            $spg = $g ? $r->stl / $g : null;
            $bpg = $g ? $r->blk / $g : null;
            $tpg = $g ? $r->tov / $g : null;

            $fg_pct = ($r->fga ?? 0) > 0 ? $r->fgm / $r->fga : null;
            $tp_pct = ($r->tpa ?? 0) > 0 ? $r->tpm / $r->tpa : null;
            $ft_pct = ($r->fta ?? 0) > 0 ? $r->ftm / $r->fta : null;

            $fmt = fn($v,$p=false)=>$v===null?'—':($p?number_format($v*100,1).'%' : number_format($v,1));

            return (object)[
                'season'      => (int)$r->season,
                'player_id'   => (int)$r->player_id,
                'team_id'     => (int)$r->team_id,
                'player_name' => $r->player_name,
                'headshot'    => $r->player_photo,
                'team_name'   => $r->team_name,
                'team_logo'   => $r->team_logo,
                'g'           => (int)$r->g,
                'wins'        => (int)$r->wins,
                'ppg'         => $fmt($ppg),
                'rpg'         => $fmt($rpg),
                'apg'         => $fmt($apg),
                'spg'         => $fmt($spg),
                'bpg'         => $fmt($bpg),
                'tpg'         => $fmt($tpg),
                'fg_pct'      => $fmt($fg_pct,true),
                'tp_pct'      => $fmt($tp_pct,true),
                'ft_pct'      => $fmt($ft_pct,true),

                // raw values for payload
                '_raw_ppg' => $ppg, '_raw_rpg'=>$rpg, '_raw_apg'=>$apg, '_raw_spg'=>$spg,
                '_raw_bpg' => $bpg, '_raw_tpg'=>$tpg, '_raw_fg'=>$fg_pct, '_raw_tp'=>$tp_pct, '_raw_ft'=>$ft_pct
            ];
        });

        // Build paginator-like meta for both
        $nbaMeta = [
            'total' => $nbaTotal,
            'per'   => $nbaPer,
            'page'  => $nbaPage,
            'last'  => max((int)ceil($nbaTotal / $nbaPer), 1),
        ];
        $lbsMeta = [
            'total' => $lbsTotal,
            'per'   => $lbsPer,
            'page'  => $lbsPage,
            'last'  => max((int)ceil($lbsTotal / $lbsPer), 1),
        ];

        return view('nba-lbs_compare', [
            'seasons' => $seasons,
            'from'    => $from,
            'to'      => $to,
            'q'       => $q,
            'nba'     => $nba,
            'lbs'     => $lbs,
            'nbaMeta' => $nbaMeta,
            'lbsMeta' => $lbsMeta,
        ]);
    }







    public function teamsExplorer(Request $request)
    {
        // -------- Seasons (union of NBA standings + LBS games) --------
        $nbaSeasons = NbaStanding::query()
            ->select('season')->distinct()->pluck('season')->toArray();

        $lbsSeasons = DB::table('games')
            ->selectRaw('DISTINCT YEAR(date) as y')->pluck('y')->toArray();

        $seasons = collect(array_unique(array_merge($nbaSeasons, $lbsSeasons)))
            ->filter()->sortDesc()->values();

        $minSeason = $seasons->isNotEmpty() ? $seasons->min() : (int)date('Y');
        $maxSeason = $seasons->isNotEmpty() ? $seasons->max() : (int)date('Y');

        $from = (int) $request->input('from', $minSeason);
        $to   = (int) $request->input('to',   $maxSeason);
        if ($from > $to) { [$from, $to] = [$to, $from]; }

        $teamQuery = trim((string) $request->input('q', ''));

        // --------------------------------------------------------------
        // NBA TEAMS: pull from nba_standings (same way as your explorer)
        // --------------------------------------------------------------
        $nbaQ = NbaStanding::query()
            ->when($from, fn($q)=>$q->where('season','>=',$from))
            ->when($to,   fn($q)=>$q->where('season','<=',$to))
            ->when($teamQuery !== '', function ($qq) use ($teamQuery) {
                $qq->where(function ($sub) use ($teamQuery) {
                    $sub->where('team_name', 'like', "%{$teamQuery}%")
                        ->orWhere('abbreviation', 'like', "%{$teamQuery}%");
                });
            })
            ->orderBy('season','desc')->orderBy('team_name')
            ->get();

        // map logos by team external_id
        $teamIds = $nbaQ->pluck('team_id')->unique()->values();
        $teams   = NbaTeam::whereIn('external_id', $teamIds)->get(['external_id','abbreviation','logo']);
        $logoMap = [];
        foreach ($teams as $t) {
            $abbr = strtolower($t->abbreviation ?? '');
            $fallback = $abbr ? "https://a.espncdn.com/i/teamlogos/nba/500/{$abbr}.png" : null;
            $logoMap[$t->external_id] = $t->logo ?: $fallback;
        }

        $nba = $nbaQ->map(function ($r) use ($logoMap) {
            $winPct = $r->win_percent;
            $ppg    = $r->avg_points_for;
            $oppPpg = $r->avg_points_against;
            $diff   = $r->point_differential;

            return (object)[
                'season'      => (int)$r->season,
                'team_id'     => (int)$r->team_id,
                'team_name'   => $r->team_name,
                'team_logo'   => $logoMap[$r->team_id] ?? null,
                'wins'        => (int)$r->wins,
                'losses'      => (int)$r->losses,
                'win_percent' => $winPct,
                'ppg'         => $ppg,
                'opp_ppg'     => $oppPpg,
                'diff'        => $diff,

                // formatted for table
                'win_percent_fmt' => $winPct !== null ? number_format($winPct * 100, 1) . '%' : '—',
                'ppg_fmt'         => $ppg    !== null ? number_format($ppg, 1)    : '—',
                'opp_ppg_fmt'     => $oppPpg !== null ? number_format($oppPpg, 1) : '—',
                'diff_txt'        => $diff !== null ? (($diff >= 0 ? '+' : '') . $diff) : '—',
                'diff_class'      => $diff !== null ? ($diff >= 0 ? 'text-[#84CC16]' : 'text-[#F97316]') : 'text-gray-300',

                // for compare payload
                '_key' => "NBA:T:{$r->team_id}:{$r->season}",
            ];
        });

        // --------------------------------------------------------------
        // LBS TEAMS: union team1/team2 from games (your exact approach)
        // --------------------------------------------------------------
        $t1_pts = "COALESCE(team1_q1+team1_q2+team1_q3+team1_q4, CAST(SUBSTRING_INDEX(score,'-',1) AS UNSIGNED))";
        $t2_pts = "COALESCE(team2_q1+team2_q2+team2_q3+team2_q4, CAST(SUBSTRING_INDEX(score,'-',-1) AS UNSIGNED))";

        $q1 = DB::table('games as g')
            ->join('teams as t', 't.id', '=', 'g.team1_id')
            ->selectRaw("
                t.id as team_id, t.name as team_name, t.logo as team_logo,
                YEAR(g.date) as season,
                COUNT(*) as games,
                SUM(CASE WHEN g.winner_id = t.id THEN 1 ELSE 0 END) as wins,
                SUM(CASE WHEN g.winner_id IS NOT NULL AND g.winner_id <> t.id THEN 1 ELSE 0 END) as losses,
                SUM($t1_pts) as points_for,
                SUM($t2_pts) as points_against
            ")
            ->when($from, fn($q)=>$q->whereRaw('YEAR(g.date) >= ?',[$from]))
            ->when($to,   fn($q)=>$q->whereRaw('YEAR(g.date) <= ?',[$to]))
            ->groupBy('team_id','team_name','team_logo','season');

        $q2 = DB::table('games as g')
            ->join('teams as t', 't.id', '=', 'g.team2_id')
            ->selectRaw("
                t.id as team_id, t.name as team_name, t.logo as team_logo,
                YEAR(g.date) as season,
                COUNT(*) as games,
                SUM(CASE WHEN g.winner_id = t.id THEN 1 ELSE 0 END) as wins,
                SUM(CASE WHEN g.winner_id IS NOT NULL AND g.winner_id <> t.id THEN 1 ELSE 0 END) as losses,
                SUM($t2_pts) as points_for,
                SUM($t1_pts) as points_against
            ")
            ->when($from, fn($q)=>$q->whereRaw('YEAR(g.date) >= ?',[$from]))
            ->when($to,   fn($q)=>$q->whereRaw('YEAR(g.date) <= ?',[$to]))
            ->groupBy('team_id','team_name','team_logo','season');

        $lbsUnion = DB::query()->fromSub($q1->unionAll($q2), 'u')
            ->selectRaw("
                team_id, team_name, team_logo, season,
                SUM(games) as games, SUM(wins) as wins, SUM(losses) as losses,
                SUM(points_for) as points_for, SUM(points_against) as points_against
            ")
            ->groupBy('team_id','team_name','team_logo','season');

        if ($teamQuery !== '') {
            $like = '%'.strtolower($teamQuery).'%';
            $lbsUnion->whereRaw('LOWER(team_name) LIKE ?', [$like]);
        }

        $lbsRaw = $lbsUnion->orderByDesc('season')->orderBy('team_name')->get();

        $lbs = $lbsRaw->map(function ($r) {
            $games  = max((int)$r->games,0);
            $wins   = (int)$r->wins;
            $losses = (int)$r->losses;
            $pf     = (int)$r->points_for;
            $pa     = (int)$r->points_against;

            $wp     = ($wins+$losses)>0 ? $wins/($wins+$losses) : null;
            $ppg    = $games>0 ? $pf/$games : null;
            $oppPpg = $games>0 ? $pa/$games : null;
            $diff   = ($wins+$losses)>0 ? ($pf - $pa) : null;

            return (object)[
                'season'      => (int)$r->season,
                'team_id'     => (int)$r->team_id,
                'team_name'   => $r->team_name,
                'team_logo'   => $r->team_logo,
                'wins'        => $wins,
                'losses'      => $losses,
                'win_percent' => $wp,
                'ppg'         => $ppg,
                'opp_ppg'     => $oppPpg,
                'diff'        => $diff,

                'win_percent_fmt' => $wp   !== null ? number_format($wp*100,1).'%' : '—',
                'ppg_fmt'         => $ppg  !== null ? number_format($ppg,1)       : '—',
                'opp_ppg_fmt'     => $oppPpg!== null ? number_format($oppPpg,1)    : '—',
                'diff_txt'        => $diff !== null ? (($diff>=0?'+':'').$diff)    : '—',
                'diff_class'      => $diff !== null ? ($diff>=0?'text-[#84CC16]':'text-[#F97316]') : 'text-gray-300',

                '_key' => "LBS:T:{$r->team_id}:{$r->season}",
            ];
        });

        // simple paginator-like meta (client already custom)
        $nbaPer = min(max((int)$request->query('nba_per', 25), 10), 200);
        $lbsPer = min(max((int)$request->query('lbs_per', 25), 10), 200);
        $nbaPage= max((int)$request->query('nba_page', 1), 1);
        $lbsPage= max((int)$request->query('lbs_page', 1), 1);

        $nbaTotal = $nba->count();
        $lbsTotal = $lbs->count();
        $nba = $nba->slice(($nbaPage-1)*$nbaPer, $nbaPer)->values();
        $lbs = $lbs->slice(($lbsPage-1)*$lbsPer, $lbsPer)->values();

        $nbaMeta = ['total'=>$nbaTotal,'per'=>$nbaPer,'page'=>$nbaPage,'last'=>max((int)ceil($nbaTotal/$nbaPer),1)];
        $lbsMeta = ['total'=>$lbsTotal,'per'=>$lbsPer,'page'=>$lbsPage,'last'=>max((int)ceil($lbsTotal/$lbsPer),1)];

        return view('nba-lbs_teams_compare', [
            'seasons'=>$seasons,
            'from'=>$from,'to'=>$to,'q'=>$teamQuery,
            'nba'=>$nba,'lbs'=>$lbs,
            'nbaMeta'=>$nbaMeta,'lbsMeta'=>$lbsMeta,
        ]);
    }

    
}
