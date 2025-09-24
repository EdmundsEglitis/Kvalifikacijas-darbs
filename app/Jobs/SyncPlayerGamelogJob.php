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

class SyncPlayerGamelogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $playerId;

    public function __construct(int $playerId)
    {
        $this->playerId = $playerId;
    }

    public function handle(NbaService $nbaService): void
    {
        set_time_limit(0);

        // Fetch gamelog from API
        $gamelog = $nbaService->playerGameLog($this->playerId);

        if (empty($gamelog)) {
            return;
        }

        $labels      = $gamelog['labels'] ?? [];
        $eventsMeta  = $gamelog['events'] ?? [];
        $seasonTypes = $gamelog['seasonTypes'] ?? [];

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

                    // Pair labels with values
                    $columns = [];
                    foreach ($labels as $i => $label) {
                        $columns[$label] = $stats[$i] ?? null;
                    }

                    $meta = $eventsMeta[$eventId] ?? [];

                    // Prepare payload
                    $payload = [
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
                    ];

                    // Save to DB
                    NbaPlayerGamelog::updateOrCreate(
                        [
                            'player_external_id' => $this->playerId,
                            'event_id'           => $eventId,
                        ],
                        $payload
                    );
                }
            }
        }
    }
}
