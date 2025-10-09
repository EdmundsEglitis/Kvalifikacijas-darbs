<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\League;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Hard-coded main leagues
        collect([
            'LBL&LBSL',
            'LJBL',
            'IZLASES',
            'REĢIONĀLIE TURNĪRI',
        ])->each(fn($name) => League::firstOrCreate(['name' => $name], ['parent_id' => null]));

        // 2) Teams + players (images saved to disk)
        $this->call(LeagueTeamPlayerSeeder::class);

        // 3) Games: many past+future (stats only for past)
        $this->call(GameAndStatsSeeder::class);

        // 4) News (hero + secondary + slots)
        $this->call(NewsSeeder::class);

        $this->call(\Database\Seeders\BackfillPlayerGameStatsSeeder::class);
    }
}
