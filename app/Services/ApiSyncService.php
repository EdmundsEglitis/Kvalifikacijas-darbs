<?php

namespace App\Services;
use Carbon\Carbon;
use App\Models\NbaPlayer;
use App\Models\NbaTeam;
use App\Models\NbaGame;
use App\Models\NbaPlayerGamelog;
use App\Services\NbaService; // your existing service
use App\Jobs\SyncPlayerDetailJob;
use App\Jobs\SyncPlayerGamelogJob;
class ApiSyncService
{
    protected NbaService $nbaService;

    public function __construct(NbaService $nbaService)
    {
        $this->nbaService = $nbaService;
    }

    public function sync()
    {
        //$this->syncPlayers();
        //$this->syncTeams();
        //$this->syncUpcomingGames();
        //$this->syncPlayerDetails();
        $this->syncPlayerGamelogs();

        // $this->syncGames(); // optional if you want game syncing too
    }

    protected function syncPlayers(): void
    {
        $allPlayers = $this->nbaService->allPlayersFromLoop();

        foreach ($allPlayers as $player) {
            
            NbaPlayer::updateOrCreate(
                        ['uid' => $player['uid']], // unique identifier from API
                        [
                        'external_id'   => $player['id'],
                        'guid'          => $player['guid'] ?? null,
                        'uid'           => $player['uid'],
                        'first_name'    => $player['firstName'],
                        'last_name'     => $player['lastName'],
                        'full_name'     => $player['fullName'],
                        'display_weight'=> $player['displayWeight'] ?? null,
                        'display_height'=> $player['displayHeight'] ?? null,
                        'age'           => $player['age'] ?? null,
                        'salary'        => $player['salary'] ?? null,
                        'image'         => $player['image'] ?? null,
                        'team_id'       => $player['teamId'] ?? null,
                        'team_name'     => $player['teamName'] ?? null,
                        'team_logo'     => $player['teamLogo'] ?? null,
                    ]
            );
        }
    }
    public function syncTeams(): void
        {
            $teams = $this->nbaService->allTeams();

                foreach ($teams as $team) {
                    NbaTeam::updateOrCreate(
                        ['external_id' => $team['id']],
                        [
                            'name'        => $team['name'],
                            'short_name'  => $team['shortName'] ?? null,
                            'abbreviation'=> $team['abbrev'] ?? null,
                            'logo'        => $team['logo'] ?? null,
                            'logo_dark'   => $team['logoDark'] ?? null,
                            'url'         => $team['href'] ?? null,
                        ]
                    );
                }
            }
            public function syncUpcomingGames(): void
            {
                set_time_limit(0);
                $games = $this->nbaService->upcomingGames(); // Your method to fetch upcoming games

                foreach ($games as $game) {
                    $tipoff = isset($game['tipoff']) ? Carbon::parse($game['tipoff'])->toDateTimeString() : null;

                    NbaGame::updateOrCreate(
                        ['external_id' => $game['id']],
                        [
                            'schedule_date'   => $game['scheduleDate'] ?? null,
                            'tipoff'          => $tipoff, // use the parsed tipoff
                            'status'          => $game['status'] ?? null,
                            'venue'           => $game['venue'] ?? null,
                            'city'            => $game['city'] ?? null,
                            'home_team_id'    => $game['homeTeam']['id'] ?? null,
                            'home_team_name'  => $game['homeTeam']['name'] ?? null,
                            'home_team_short' => $game['homeTeam']['short'] ?? null,
                            'home_team_logo'  => $game['homeTeam']['logo'] ?? null,
                            'away_team_id'    => $game['awayTeam']['id'] ?? null,
                            'away_team_name'  => $game['awayTeam']['name'] ?? null,
                            'away_team_short' => $game['awayTeam']['short'] ?? null,
                            'away_team_logo'  => $game['awayTeam']['logo'] ?? null,
                        ]
                    );
                }
            }
            public function syncPlayerDetails(): void
            {
                set_time_limit(0);
                \App\Models\NbaPlayer::chunk(10, function ($players) {
                    foreach ($players as $player) {
                        SyncPlayerDetailJob::dispatch($player->external_id);
                    }
                });
            }

public function syncPlayerGamelogs(): void
{
    // Take only the first 10 players for testing
    NbaPlayer::limit(10)->get()->each(function ($player) {
        \App\Jobs\SyncPlayerGamelogJob::dispatch($player->id);
    });

    echo "Dispatched gamelog jobs for 10 test players.\n";
}


        }
