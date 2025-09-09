<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\League;
use App\Models\Team;
use App\Models\Player;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create 4 fixed main leagues
        $mainLeagues = collect([
            'LBL&LBSL',
            'LJBL',
            'IZLASES',
            'REĢIONĀLIE TURNĪRI'
        ])->map(fn($name) => League::factory()->create(['name' => $name]));

        foreach ($mainLeagues as $main) {

            // 2. Create 2-3 sub-leagues per main league
            $subLeagues = League::factory(rand(2,3))
                ->subLeague($main)
                ->create();

            foreach ($subLeagues as $sub) {

                // 3. Each sub-league has 4 teams
                $teams = Team::factory(4)
                    ->create(['league_id' => $sub->id]);

                foreach ($teams as $team) {

                    // 4. Each team has 12 players
                    Player::factory(12)
                        ->create([
                            'team_id' => $team->id,
                            'league_id' => $sub->id,
                        ]);
                }
            }
        }
    }
}
