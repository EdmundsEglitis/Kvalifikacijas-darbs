<?php

namespace App\Jobs;

use App\Models\NbaPlayer;
use App\Models\NbaPlayerGamelog;
use App\Services\NbaService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncPlayerGamelogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var int|string */
    public $playerId;

    /**
     * @param int|string $playerId
     */
    public function __construct($playerId)
    {
        $this->playerId = $playerId;
    }

    public function handle(NbaService $nbaService)
    {
        $player = NbaPlayer::find($this->playerId);
        if (!$player) {
            Log::warning("SyncPlayerGamelogJob: Player not found for ID {$this->playerId}");
            return;
        }

        $gamelog = $nbaService->playerGameLog($player->external_id);

        if (empty($gamelog['seasonTypes'])) {
            Log::info("SyncPlayerGamelogJob: No events for player {$player->id}");
            return;
        }

        foreach ($gamelog['seasonTypes'] as $season) {
            $seasonName = isset($season['displayName']) ? $season['displayName'] : null;

            foreach (isset($season['categories']) ? $season['categories'] : [] as $category) {
                if (!isset($category['type']) || $category['type'] !== 'event') {
                    continue;
                }

                foreach (isset($category['events']) ? $category['events'] : [] as $event) {
                    try {
                        
                        $eventId = null;
                        if (isset($event['eventId'])) {
                            $eventId = $event['eventId'];
                        } elseif (isset($event['id'])) {
                            $eventId = $event['id'];
                        }

                        if (!$eventId) {
                            Log::warning("SyncPlayerGamelogJob: Missing eventId for player {$player->id}. Raw event: " . json_encode($event));
                            continue;
                        }

                        $meta = isset($gamelog['events'][$eventId]) ? $gamelog['events'][$eventId] : null;
                        if (!$meta) {
                            Log::warning("SyncPlayerGamelogJob: No metadata found for event {$eventId} (player {$player->id})");
                            continue;
                        }

                        
                        $stats = [];
                        if (isset($event['stats'])) {
                            foreach ($event['stats'] as $stat) {
                                if (isset($stat['abbreviation']) && isset($stat['displayValue'])) {
                                    $stats[$stat['abbreviation']] = $stat['displayValue'];
                                }
                            }
                        }

                        NbaPlayerGamelog::updateOrCreate(
                            [
                                'player_id' => $player->id,
                                'event_id'  => $eventId,
                            ],
                            [
                                'minutes'       => isset($stats['MIN']) ? $stats['MIN'] : null,
                                'fg'            => isset($stats['FG']) ? $stats['FG'] : null,
                                'fg_pct'        => isset($stats['FG%']) ? (float)$stats['FG%'] : null,
                                'three_pt'      => isset($stats['3PT']) ? $stats['3PT'] : null,
                                'three_pt_pct'  => isset($stats['3P%']) ? (float)$stats['3P%'] : null,
                                'ft'            => isset($stats['FT']) ? $stats['FT'] : null,
                                'ft_pct'        => isset($stats['FT%']) ? (float)$stats['FT%'] : null,
                                'rebounds'      => isset($stats['REB']) ? $stats['REB'] : null,
                                'assists'       => isset($stats['AST']) ? $stats['AST'] : null,
                                'steals'        => isset($stats['STL']) ? $stats['STL'] : null,
                                'blocks'        => isset($stats['BLK']) ? $stats['BLK'] : null,
                                'turnovers'     => isset($stats['TO']) ? $stats['TO'] : null,
                                'fouls'         => isset($stats['PF']) ? $stats['PF'] : null,
                                'points'        => isset($stats['PTS']) ? $stats['PTS'] : null,
                                'updated_at'    => now(),
                            ]
                        );

                        Log::info("SyncPlayerGamelogJob: Saved gamelog for player {$player->id}, event {$eventId}");
                    } catch (\Throwable $e) {
                        $eventKey = isset($event['eventId']) ? $event['eventId'] : 'unknown';
                        Log::error("SyncPlayerGamelogJob failed for player {$player->id}, event {$eventKey}: {$e->getMessage()}");
                    }
                }
            }
        }
    }
}
