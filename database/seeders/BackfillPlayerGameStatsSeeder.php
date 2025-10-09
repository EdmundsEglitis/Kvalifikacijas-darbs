<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class BackfillPlayerGameStatsSeeder extends Seeder
{
    public function run(): void
    {
        // Process games in chunks to keep memory low
        DB::table('games')
            ->select('id', 'team1_id', 'team2_id', 'date')
            ->orderBy('id')
            ->chunkById(200, function ($games) {
                foreach ($games as $g) {
                    $this->ensureTeamHasStatsForGame($g->id, $g->team1_id);
                    $this->ensureTeamHasStatsForGame($g->id, $g->team2_id);
                }
            });
    }

    /**
     * Ensure every player of $teamId has a stats row in player_game_stats for $gameId.
     * Inserts only missing (game_id, player_id) pairs with zeros/DNP.
     */
    protected function ensureTeamHasStatsForGame(int $gameId, ?int $teamId): void
    {
        if (!$teamId) return;

        // Team roster (adjust table/columns if yours differ)
        $playerIds = DB::table('players')
            ->where('team_id', $teamId)
            ->pluck('id')
            ->all();

        if (empty($playerIds)) return;

        // Existing rows for that game
        $existing = DB::table('player_game_stats')
            ->where('game_id', $gameId)
            ->whereIn('player_id', $playerIds)
            ->pluck('player_id')
            ->all();

        // Determine which players are missing a stats row
        $missing = array_values(array_diff($playerIds, $existing));
        if (empty($missing)) return;

        $now = Carbon::now();
        $rows = [];
        foreach ($missing as $pid) {
            $rows[] = [
                'game_id'     => $gameId,
                'player_id'   => $pid,
                'team_id'     => $teamId,

                // Your schema (zeros + DNP)
                'minutes'     => '0:00',
                'points'      => 0,

                'fgm2'        => 0,
                'fga2'        => 0,
                'fgm3'        => 0,
                'fga3'        => 0,
                'ftm'         => 0,
                'fta'         => 0,

                'oreb'        => 0,
                'dreb'        => 0,
                'reb'         => 0,

                'ast'         => 0,
                'tov'         => 0,
                'stl'         => 0,
                'blk'         => 0,
                'pf'          => 0,

                'eff'         => 0,
                'plus_minus'  => 0,

                'status'      => 'dnp',   // matches your enum ['played','dnp']

                'created_at'  => $now,
                'updated_at'  => $now,
            ];
        }

        // Bulk insert new rows
        DB::table('player_game_stats')->insert($rows);
    }
}
