<?php

namespace App\Http\Controllers\Nba\Games;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class GameShowController extends Controller
{
    public function show(int $eventId)
    {
        $payload = Cache::remember("nba_game:$eventId", 3600, function () use ($eventId) {
            // Pull ALL player lines for this event (no limits)
            $rows = DB::table('nba_player_game_logs as l')
                ->leftJoin('nba_players as p', 'p.external_id', '=', 'l.player_external_id')
                ->where('l.event_id', $eventId)
                ->select([
                    'l.event_id',
                    'l.game_date',
                    'l.result',        // 'W' or 'L' from player's perspective
                    'l.score',
                    'l.opponent_name',
                    'l.opponent_logo',

                    'p.external_id as player_id',
                    DB::raw("TRIM(CONCAT(COALESCE(p.first_name,''),' ',COALESCE(p.last_name,''))) as player_name"),
                    'p.image as headshot',

                    'l.minutes',
                    'l.fg', 'l.fg_pct',
                    'l.three_pt', 'l.three_pt_pct',
                    'l.ft', 'l.ft_pct',
                    'l.rebounds', 'l.assists', 'l.steals', 'l.blocks', 'l.turnovers', 'l.fouls', 'l.points',
                ])
                ->orderByDesc('l.points') // just a presentational default
                ->get();

            if ($rows->isEmpty()) {
                return null;
            }

            // Split strictly by game result — this is stable for a single event
            $byResult = $rows->groupBy(fn($r) => strtoupper(trim((string)$r->result)) === 'W' ? 'W' : 'L');

            $W = $byResult->get('W', collect()); // winning team rows
            $L = $byResult->get('L', collect()); // losing team rows

            // Fallback: if something odd, just split the set into two halves
            if ($W->isEmpty() || $L->isEmpty()) {
                $half = (int) ceil($rows->count() / 2);
                $W = $rows->slice(0, $half);
                $L = $rows->slice($half);
            }

            // Team names/logos are cross-derived:
            // - Winners faced the Losers -> losers' rows contain winners' name in opponent_name and vice-versa.
            $winnerName = $L->first()->opponent_name ?? 'Winner';
            $winnerLogo = $L->first()->opponent_logo ?? null;
            $loserName  = $W->first()->opponent_name ?? 'Loser';
            $loserLogo  = $W->first()->opponent_logo ?? null;

            // Helpers
            $pair = function ($s) {
                if (!is_string($s) || strpos($s, '-') === false) return [0, 0];
                [$m, $a] = explode('-', $s, 2);
                return [max((int)$m, 0), max((int)$a, 0)];
            };
            $pct = fn($m, $a) => $a > 0 ? round(($m / $a) * 100, 1) : null;

            $buildTeam = function ($group, string $teamName, ?string $teamLogo) use ($pair, $pct) {
                $fgM = $fgA = $tpM = $tpA = $ftM = $ftA = 0;

                foreach ($group as $r) {
                    [$m, $a] = $pair($r->fg);       $fgM += $m; $fgA += $a;
                    [$m, $a] = $pair($r->three_pt); $tpM += $m; $tpA += $a;
                    [$m, $a] = $pair($r->ft);       $ftM += $m; $ftA += $a;
                }

                return [
                    'team' => $teamName,
                    'logo' => $teamLogo,
                    'players' => $group->map(fn($r) => [
                        'id'   => $r->player_id,
                        'name' => $r->player_name ?: '—',
                        'img'  => $r->headshot,
                        'min'  => $r->minutes,
                        'fg'   => $r->fg,       'fgp'  => $r->fg_pct,
                        'tp'   => $r->three_pt, 'tpp'  => $r->three_pt_pct,
                        'ft'   => $r->ft,       'ftp'  => $r->ft_pct,
                        'reb'  => $r->rebounds, 'ast'  => $r->assists, 'stl' => $r->steals,
                        'blk'  => $r->blocks,   'tov'  => $r->turnovers, 'pf' => $r->fouls,
                        'pts'  => $r->points,
                    ])->values(),
                    'totals' => [
                        'pts' => (int)$group->sum('points'),
                        'reb' => (int)$group->sum('rebounds'),
                        'ast' => (int)$group->sum('assists'),
                        'stl' => (int)$group->sum('steals'),
                        'blk' => (int)$group->sum('blocks'),
                        'tov' => (int)$group->sum('turnovers'),
                        'pf'  => (int)$group->sum('fouls'),
                        'fg'  => ['m' => $fgM, 'a' => $fgA, 'pct' => $pct($fgM, $fgA)],
                        'tp'  => ['m' => $tpM, 'a' => $tpA, 'pct' => $pct($tpM, $tpA)],
                        'ft'  => ['m' => $ftM, 'a' => $ftA, 'pct' => $pct($ftM, $ftA)],
                    ],
                ];
            };

            $A = $buildTeam($W, $winnerName, $winnerLogo);
            $B = $buildTeam($L, $loserName,  $loserLogo);

            // Score: prefer summed points (trust logs), fallback to any row's score string
            $sumScore = "{$A['totals']['pts']}-{$B['totals']['pts']}";
            $anyRow   = $rows->first();
            $scoreStr = $anyRow->score ?: $sumScore;

            $winnerIdx = $A['totals']['pts'] === $B['totals']['pts']
                ? null
                : ($A['totals']['pts'] > $B['totals']['pts'] ? 0 : 1);

            return [
                'game' => [
                    'event_id' => $eventId,
                    'date'     => $anyRow->game_date,
                    'score'    => $scoreStr,
                    'winner'   => $winnerIdx,
                ],
                'A' => $A, // winners
                'B' => $B, // losers
            ];
        });

        abort_if(!$payload, 404);

        return view('nba.games.show', $payload);
    }
}
