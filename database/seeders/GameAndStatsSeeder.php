<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use App\Models\League;
use App\Models\Team;
use App\Models\Player;
use App\Models\Game;
use App\Models\PlayerGameStat;

class GameAndStatsSeeder extends Seeder
{
    // Scheduling knobs
    private const ROUNDS_PER_PAIR     = 3;  // ≈15 games per team in a 6-team sub-league
    private const PAST_DAYS_MAX       = 90;
    private const FUTURE_DAYS_MAX     = 30;
    private const FUTURE_PROBABILITY  = 30; // 30% future, 70% past

    public function run(): void
    {
        foreach (League::all() as $league) {
            $teams = Team::where('league_id', $league->id)->pluck('id')->values();
            $n = $teams->count();
            if ($n < 2) continue;

            for ($i=0; $i<$n; $i++) {
                for ($j=$i+1; $j<$n; $j++) {
                    $team1Id = $teams[$i];
                    $team2Id = $teams[$j];

                    for ($round = 1; $round <= self::ROUNDS_PER_PAIR; $round++) {
                        $this->createMatch($team1Id, $team2Id, $round);
                    }
                }
            }
        }
    }

    private function createMatch(int $team1Id, int $team2Id, int $round): void
    {
        $isFuture = rand(1,100) <= self::FUTURE_PROBABILITY;

        if ($isFuture) {
            // FUTURE game — no score/winner/quarters/stats
            $date = Carbon::now()
                ->addDays(rand(2, self::FUTURE_DAYS_MAX) + $round) // spread rounds
                ->setTime(rand(17, 21), rand(0,1) ? 0 : 30);

            Game::create([
                'date'      => $date,
                'team1_id'  => $team1Id,
                'team2_id'  => $team2Id,
                'score'     => null,
                'team1_q1'  => null, 'team1_q2' => null, 'team1_q3' => null, 'team1_q4' => null,
                'team2_q1'  => null, 'team2_q2' => null, 'team2_q3' => null, 'team2_q4' => null,
                'winner_id' => null,
            ]);

            return;
        }

        // PAST game — full data
        $date = Carbon::now()
            ->subDays(rand(5, self::PAST_DAYS_MAX) + $round)
            ->setTime(rand(17, 21), rand(0,1) ? 0 : 30);

        // team totals & quarters
        $t1Total = rand(70, 98);
        $t2Total = rand(70, 98);
        [$t1q1,$t1q2,$t1q3,$t1q4] = $this->quartersFromTotal($t1Total);
        [$t2q1,$t2q2,$t2q3,$t2q4] = $this->quartersFromTotal($t2Total);
        $winnerId = $t1Total >= $t2Total ? $team1Id : $team2Id;

        $game = Game::create([
            'date'      => $date,
            'team1_id'  => $team1Id,
            'team2_id'  => $team2Id,
            'score'     => "{$t1Total}-{$t2Total}",
            'team1_q1'  => $t1q1, 'team1_q2' => $t1q2, 'team1_q3' => $t1q3, 'team1_q4' => $t1q4,
            'team2_q1'  => $t2q1, 'team2_q2' => $t2q2, 'team2_q3' => $t2q3, 'team2_q4' => $t2q4,
            'winner_id' => $winnerId,
        ]);

        // Player stats that exactly sum to team totals
        $this->seedTeamBoxScore($game, $team1Id, $t1Total, $winnerId === $team1Id);
        $this->seedTeamBoxScore($game, $team2Id, $t2Total, $winnerId === $team2Id);
    }

    private function quartersFromTotal(int $total): array
    {
        $w = [rand(22,30), rand(22,30), rand(22,30), rand(22,30)];
        $sum = array_sum($w);
        $raw = array_map(fn($x)=>$x/$sum*$total, $w);
        $ints = array_map(fn($x)=>floor($x), $raw);
        $rem = $total - array_sum($ints);
        $idx = array_keys($raw);
        usort($idx, fn($a,$b)=>($raw[$b]-floor($raw[$b]))<=>($raw[$a]-floor($raw[$a])));
        for ($k=0; $k<$rem; $k++) $ints[$idx[$k%4]]++;
        return $ints;
    }

    private function seedTeamBoxScore(Game $game, int $teamId, int $teamPoints, bool $won): void
    {
        $players = Player::where('team_id', $teamId)->inRandomOrder()->get();
        if ($players->isEmpty()) return;

        // Rotation 9–10 players
        $rotation = $players->take(rand(9,10))->values();

        // usage weights (two "stars")
        $weights = [];
        foreach ($rotation as $idx => $p) {
            $base = mt_rand(5, 15);
            if ($idx < 2) $base += mt_rand(10,15);
            $weights[] = $base;
        }
        $wSum = array_sum($weights);
        $raw  = array_map(fn($w) => $w / $wSum * $teamPoints, $weights);

        // integer points per player (exact sum)
        $pts = array_map('floor', $raw);
        $rem = $teamPoints - array_sum($pts);
        $fracIdx = array_keys($raw);
        usort($fracIdx, fn($a,$b) => ($raw[$b]-floor($raw[$b])) <=> ($raw[$a]-floor($raw[$a])));
        for ($k=0; $k<$rem; $k++) { $pts[$fracIdx[$k % count($pts)]]++; }

        // minutes
        $minutes = $this->minutesForRotation(count($rotation));

        foreach ($rotation as $i => $player) {
            $this->insertConsistentLine(
                gameId:    $game->id,
                teamId:    $teamId,
                playerId:  $player->id,
                points:    $pts[$i],
                mins:      $minutes[$i],
                isWinner:  $won
            );
        }

        // DNPs
        $dnps = $players->slice(count($rotation), 2);
        foreach ($dnps as $p) {
            PlayerGameStat::create([
                'game_id'  => $game->id,
                'player_id'=> $p->id,
                'team_id'  => $teamId,
                'minutes'  => '0:00',
                'status'   => 'dnp',
            ]);
        }
    }

    private function minutesForRotation(int $n): array
    {
        $mins = [];
        for ($i=0;$i<$n;$i++){
            if ($i<2) $mins[] = rand(28,36);
            elseif ($i<5) $mins[] = rand(22,30);
            else $mins[] = rand(10,20);
        }
        return array_map(fn($m) => sprintf('%d:%02d', $m, rand(0,59)), $mins);
    }

    private function insertConsistentLine(int $gameId, int $teamId, int $playerId, int $points, string $mins, bool $isWinner): void
    {
        $want3 = (int) round($points * (rand(20,40)/100));
        $want3 -= $want3 % 3;
        if ($want3 > $points) $want3 = $points - ($points % 3);
        $fgm3 = $want3 > 0 ? intdiv($want3, 3) : 0;

        $remain = $points - 3*$fgm3;

        $ftm = min($remain, rand(0, 6));
        $remain -= $ftm;

        $fgm2 = $remain > 0 ? intdiv($remain, 2) : 0;
        if ($points !== (2*$fgm2 + 3*$fgm3 + $ftm)) {
            $delta = $points - (2*$fgm2 + 3*$fgm3 + $ftm);
            $ftm += $delta;
        }

        $fga2 = $fgm2 + ($fgm2 ? rand(1, max(2,$fgm2+3)) : rand(0,3));
        $fga3 = $fgm3 + ($fgm3 ? rand(1, max(1,$fgm3+2)) : rand(0,2));
        $fta  = $ftm  + ($ftm  ? rand(0,2) : rand(0,3));

        $reb = rand(0,12);
        $oreb = min($reb, rand(0,4));
        $dreb = $reb - $oreb;

        $ast = rand(0,10);
        $tov = rand(0,6);
        $stl = rand(0,4);
        $blk = rand(0,3);
        $pf  = rand(0,5);

        $eff = $points + $reb + $ast + $stl + $blk
             - (($fga2 - $fgm2) + ($fga3 - $fgm3) + ($fta - $ftm)) - $tov;

        $plusMinus = $isWinner ? rand(0,18) : rand(-15, 3);

        PlayerGameStat::create([
            'game_id'   => $gameId,
            'player_id' => $playerId,
            'team_id'   => $teamId,
            'minutes'   => $mins,
            'points'    => $points,
            'fgm2' => $fgm2, 'fga2' => max($fgm2, $fga2),
            'fgm3' => $fgm3, 'fga3' => max($fgm3, $fga3),
            'ftm'  => $ftm,  'fta'  => max($ftm,  $fta),
            'oreb' => $oreb, 'dreb' => $dreb, 'reb' => $reb,
            'ast'  => $ast,  'tov'  => $tov,  'stl' => $stl, 'blk' => $blk,
            'pf'   => $pf,
            'eff'  => $eff,
            'plus_minus' => $plusMinus,
            'status' => 'played',
        ]);
    }
}
