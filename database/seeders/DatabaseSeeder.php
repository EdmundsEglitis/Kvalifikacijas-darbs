<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\League;
use App\Models\Team;
use App\Models\Player;
use App\Models\Game;
use App\Models\PlayerGameStat;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create main leagues
        $mainLeagues = collect([
            'LBL&LBSL',
            'LJBL',
            'IZLASES',
            'REĢIONĀLIE TURNĪRI'
        ])->map(fn($name) => League::factory()->create(['name' => $name]));

        foreach ($mainLeagues as $main) {
            // 2. Sub-leagues
            $subLeagues = League::factory(rand(2,3))
                ->subLeague($main)
                ->create();

            foreach ($subLeagues as $sub) {
                // 3. Teams
                $teams = Team::factory(4)->create(['league_id' => $sub->id]);

                foreach ($teams as $team) {
                    // 4. Players
                    Player::factory(12)->create([
                        'team_id' => $team->id,
                        'league_id' => $sub->id,
                    ]);
                }

                // 5. Games between teams (round-robin style)
                $teamIds = $teams->pluck('id')->all();
                for ($i = 0; $i < count($teamIds); $i++) {
                    for ($j = $i + 1; $j < count($teamIds); $j++) {
                        $team1 = $teamIds[$i];
                        $team2 = $teamIds[$j];

                        $team1Score = rand(50, 120);
                        $team2Score = rand(50, 120);

                        $game = Game::create([
                            'team1_id' => $team1,
                            'team2_id' => $team2,
                            'score' => "$team1Score:$team2Score",
                            'winner_id' => $team1Score > $team2Score ? $team1 : $team2,
                            'date' => now()->subDays(rand(0, 60)),
                        ]);

                        // 6. Assign stats to some players in each team
                        foreach (Player::where('team_id', $team1)->inRandomOrder()->take(6)->get() as $player) {
                            PlayerGameStat::factory()->create([
                                'player_id' => $player->id,
                                'team_id' => $team1,
                                'game_id' => $game->id,
                            ]);
                        }
                        foreach (Player::where('team_id', $team2)->inRandomOrder()->take(6)->get() as $player) {
                            PlayerGameStat::factory()->create([
                                'player_id' => $player->id,
                                'team_id' => $team2,
                                'game_id' => $game->id,
                            ]);
                        }
                    }
                }
            }
        }
    }
}
