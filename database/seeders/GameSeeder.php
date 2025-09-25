<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use App\Models\League;
use App\Models\Team;
use App\Models\Game;
use App\Models\Player;
use App\Models\PlayerGameStat;

class GameSeeder extends Seeder
{
    public function run(): void
    {
        if (Game::count() > 0) {
            // Already have games; don’t create duplicates
            return;
        }

        // Group teams by league (fallback: all teams in one bucket)
        $leagues = League::with('teams')->get();
        if ($leagues->isEmpty()) {
            $this->seedSimpleGames();
            return;
        }

        foreach ($leagues as $league) {
            $teams = $league->teams;
            if ($teams->count() < 2) continue;

            $this->createRoundRobin($teams->all());
        }
    }

    private function createRoundRobin(array $teams): void
    {
        $n = count($teams);
        for ($i = 0; $i < $n; $i++) {
            for ($j = $i + 1; $j < $n; $j++) {
                $team1 = $teams[$i];
                $team2 = $teams[$j];

                // Make half future, half past for variety
                $date = rand(0,1)
                    ? Carbon::now()->addDays(rand(2, 21))->setTime(rand(17, 21), rand(0, 1) ? 0 : 30)
                    : Carbon::now()->subDays(rand(1, 30))->setTime(rand(17, 21), rand(0, 1) ? 0 : 30);

                // Random score & winner
                $t1 = rand(60, 95);
                $t2 = rand(60, 95);
                $winnerId = $t1 >= $t2 ? $team1->id : $team2->id;

                $game = Game::create([
                    'date'       => $date,
                    'team1_id'   => $team1->id,
                    'team2_id'   => $team2->id,
                    'score'      => "{$t1}-{$t2}",
                    'winner_id'  => $winnerId,
                ]);

                // Seed some stats for players from both teams so FKs are valid
                $this->seedStatsForTeam($game->id, $team1->id);
                $this->seedStatsForTeam($game->id, $team2->id);
            }
        }
    }

    private function seedStatsForTeam(int $gameId, int $teamId): void
    {
        $players = Player::where('team_id', $teamId)->inRandomOrder()->take(6)->get();
        foreach ($players as $p) {
            PlayerGameStat::create([
                'game_id'    => $gameId,
                'player_id'  => $p->id,
                'team_id'    => $teamId,
                'minutes'    => rand(8, 38),
                'points'     => rand(0, 28),
                'fgm2'       => rand(0, 10),
                'fga2'       => rand(0, 14),
                'fgm3'       => rand(0, 6),
                'fga3'       => rand(0, 12),
                'ftm'        => rand(0, 8),
                'fta'        => rand(0, 10),
                'oreb'       => rand(0, 4),
                'dreb'       => rand(0, 9),
                'reb'        => rand(0, 12),
                'ast'        => rand(0, 9),
                'tov'        => rand(0, 6),
                'stl'        => rand(0, 4),
                'blk'        => rand(0, 3),
                'pf'         => rand(0, 5),
                'eff'        => rand(-5, 30),
                'plus_minus' => rand(-15, 18),
                'status'     => 'played',
            ]);
        }
    }

    // Fallback: if you have no leagues set up
    private function seedSimpleGames(): void
    {
        $teams = Team::inRandomOrder()->take(6)->get();
        if ($teams->count() < 2) return;

        $this->createRoundRobin($teams->all());
    }
}
