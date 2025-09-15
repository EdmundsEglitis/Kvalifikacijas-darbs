<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PlayerGameStat;
use App\Models\Player;
use App\Models\Team;
use App\Models\Game;

class PlayerGameStatSeeder extends Seeder
{
    public function run(): void
    {
        // Get all players, teams, and games
        $players = Player::all();
        $teams = Team::all();
        $games = Game::all();

        // Loop through games and players to generate stats
        foreach ($games as $game) {
            foreach ($players as $player) {
                // Only create stats for players in either team
                if ($player->team_id === $game->team1_id || $player->team_id === $game->team2_id) {
                    PlayerGameStat::create([
                        'game_id' => $game->id,
                        'player_id' => $player->id,
                        'team_id' => $player->team_id,
                        'minutes' => rand(5, 40),
                        'points' => rand(0, 30),
                        'fgm2' => rand(0, 10),
                        'fga2' => rand(0, 15),
                        'fgm3' => rand(0, 5),
                        'fga3' => rand(0, 10),
                        'ftm' => rand(0, 10),
                        'fta' => rand(0, 12),
                        'oreb' => rand(0, 5),
                        'dreb' => rand(0, 10),
                        'reb' => rand(0, 15),
                        'ast' => rand(0, 10),
                        'tov' => rand(0, 5),
                        'stl' => rand(0, 5),
                        'blk' => rand(0, 5),
                        'pf' => rand(0, 5),
                        'eff' => rand(-5, 30),
                        'plus_minus' => rand(-15, 20),
                        'status' => ['played', 'dnp'][array_rand(['played', 'dnp'])],
                    ]);
                }
            }
        }
    }
}

