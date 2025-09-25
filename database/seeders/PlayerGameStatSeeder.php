<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use App\Models\Game;
use App\Models\Player;
use App\Models\PlayerGameStat;

class PlayerGameStatSeeder extends Seeder
{
    public function run(): void
    {
        // Load games with teams available
        $games = Game::with(['team1', 'team2'])->get();

        foreach ($games as $game) {
            // Skip if stats already exist for this game
            if (PlayerGameStat::where('game_id', $game->id)->exists()) {
                continue;
            }

            // Get 6 random players from each team (if teams exist)
            $team1Players = $game->team1
                ? Player::where('team_id', $game->team1_id)->inRandomOrder()->take(6)->get()
                : collect();

            $team2Players = $game->team2
                ? Player::where('team_id', $game->team2_id)->inRandomOrder()->take(6)->get()
                : collect();

            $this->seedTeamStats($game->id, $game->team1_id, $team1Players);
            $this->seedTeamStats($game->id, $game->team2_id, $team2Players);
        }
    }

    private function seedTeamStats(int $gameId, ?int $teamId, $players): void
    {
        if (!$teamId || $players->isEmpty()) return;

        foreach ($players as $p) {
            PlayerGameStat::create([
                'game_id'    => $gameId,
                'player_id'  => $p->id,
                'team_id'    => $teamId,

                'minutes'    => rand(8, 38),
                'points'     => rand(0, 30),

                'fgm2'       => rand(0, 10),
                'fga2'       => rand(0, 15),
                'fgm3'       => rand(0, 6),
                'fga3'       => rand(0, 12),
                'ftm'        => rand(0, 8),
                'fta'        => rand(0, 10),

                'oreb'       => rand(0, 4),
                'dreb'       => rand(0, 9),
                'reb'        => rand(0, 12),

                'ast'        => rand(0, 10),
                'tov'        => rand(0, 6),
                'stl'        => rand(0, 4),
                'blk'        => rand(0, 3),
                'pf'         => rand(0, 5),

                'eff'        => rand(-5, 35),
                'plus_minus' => rand(-15, 20),
                'status'     => Arr::random(['played', 'dnp']),
            ]);
        }
    }
}
