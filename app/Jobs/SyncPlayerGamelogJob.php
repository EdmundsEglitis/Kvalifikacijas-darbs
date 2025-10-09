<?php

namespace App\Jobs;

use App\Models\NbaPlayerGamelog;
use App\Services\NbaService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncPlayerGamelogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var array<int> */
    public array $playerIds;


    public int $timeout = 1200000;


    public int $tries = 100;


    public function __construct(array $playerIds)
    {
        $this->playerIds = $playerIds;
    }


    public function handle(NbaService $nbaService): void
    {
        set_time_limit(0);

        foreach ($this->playerIds as $playerId) {
                $this->processPlayer($nbaService, $playerId);
      
        }
    }


    protected function processPlayer(NbaService $nbaService, int $playerId): void
    {
        $gamelog = $nbaService->playerGameLog($playerId);

        if (empty($gamelog)) {
            return;
        }

        $labels      = $gamelog['labels'] ?? [];
        $eventsMeta  = $gamelog['events'] ?? [];
        $seasonTypes = $gamelog['seasonTypes'] ?? [];

        $rows = [];

        foreach ($seasonTypes as $season) {
            foreach ($season['categories'] ?? [] as $category) {
                if (($category['type'] ?? null) !== 'event' || empty($category['events'])) {
                    continue;
                }

                foreach ($category['events'] as $event) {
                    $eventId = $event['eventId'] ?? null;
                    $stats   = $event['stats'] ?? [];

                    if (!$eventId) {
                        continue;
                    }


                    $columns = [];
                    foreach ($labels as $i => $label) {
                        $columns[$label] = $stats[$i] ?? null;
                    }

                    $meta = $eventsMeta[$eventId] ?? [];

                    $rows[] = [
                        'player_external_id' => $playerId,
                        'event_id'           => $eventId,

                        'game_date'     => isset($meta['gameDate']) ? Carbon::parse($meta['gameDate'])->toDateString() : null,
                        'opponent_name' => $meta['opponent']['displayName'] ?? null,
                        'opponent_logo' => $meta['opponent']['logo'] ?? null,
                        'result'        => $meta['gameResult'] ?? null,
                        'score'         => $meta['score'] ?? null,

                        'minutes'       => $columns['MIN'] ?? null,
                        'fg'            => $columns['FG'] ?? null,
                        'fg_pct'        => isset($columns['FG%']) ? (float)$columns['FG%'] : null,
                        'three_pt'      => $columns['3PT'] ?? null,
                        'three_pt_pct'  => isset($columns['3PT%']) ? (float)$columns['3PT%'] : (isset($columns['3P%']) ? (float)$columns['3P%'] : null),
                        'ft'            => $columns['FT'] ?? null,
                        'ft_pct'        => isset($columns['FT%']) ? (float)$columns['FT%'] : null,
                        'rebounds'      => $columns['REB'] ?? null,
                        'assists'       => $columns['AST'] ?? null,
                        'steals'        => $columns['STL'] ?? null,
                        'blocks'        => $columns['BLK'] ?? null,
                        'turnovers'     => $columns['TO'] ?? null,
                        'fouls'         => $columns['PF'] ?? null,
                        'points'        => $columns['PTS'] ?? null,

                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ];
                }
            }
        }

        if (!empty($rows)) {
            NbaPlayerGamelog::upsert(
                $rows,
                ['player_external_id', 'event_id'],
                array_keys($rows[0]) 
            );
        }
    }
}
