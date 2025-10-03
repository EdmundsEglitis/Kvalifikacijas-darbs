<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
}
