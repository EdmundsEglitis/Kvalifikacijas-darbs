<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
class NbaService
{
    protected string $baseUri;
    protected string $key;
    protected int $timeout;

    public function __construct()
    {
        $this->key = config('nba.key');
        $this->baseUri = config('nba.base_uri');
        $this->timeout = config('nba.timeout');

        if (empty($this->key)) {
            throw new \Exception("NBA API key is missing. Check your .env and config/nba.php.");
        }
    }

    protected function request(string $endpoint, array $params = [])
    {
        $response = Http::timeout($this->timeout)
            ->withHeaders([
                'x-rapidapi-key' => $this->key,
                'x-rapidapi-host' => parse_url($this->baseUri, PHP_URL_HOST),
            ])
            ->get("{$this->baseUri}{$endpoint}", $params);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception("NBA API request failed: " . $response->status() . ' ' . $response->body());
    }

    public function allTeams(): array
    {
        return Cache::remember('nba:teams', 3600, function () {
            $endpoints = [
                '/nba-atlantic-team-list',
                '/nba-central-team-list',
                '/nba-southeast-team-list',
                '/nba-northwest-team-list',
                '/nba-pacific-team-list',
                '/nba-southwest-team-list',
            ];

            $teams = [];
            foreach ($endpoints as $endpoint) {
                $response = $this->request($endpoint);
                foreach ($response['response']['teamList'] ?? [] as $team) {
                    if (!empty($team['id'])) {
                        $teams[$team['id']] = $team;
                    }
                }
            }
            return $teams;
        });
    }

    public function playersByTeam(string|int $teamId): array
    {
        return Cache::remember("nba:players:team:{$teamId}", 1800, function () use ($teamId) {
            $response = $this->request('/nba-player-list', ['teamid' => (string) $teamId]);
            $players = $response['response']['PlayerList'] ?? $response['response']['playerList'] ?? [];
            return is_array($players) ? $players : [];
        });
    }

    public function allPlayersFromLoop(): array
    {
        $teams = $this->allTeams();
        $all = [];

        foreach ($teams as $teamId => $team) {
            $teamPlayers = $this->playersByTeam($teamId);
            foreach ($teamPlayers as &$p) {
                $p['teamId'] = (string) $teamId;
                $p['teamName'] = $team['name'] ?? 'Unknown';
                $p['teamLogo'] = $team['logo'] ?? null;
            }
            $all = array_merge($all, $teamPlayers);
        }

        return $all;
    }

    public function upcomingGames(): array
    {
        $start = Carbon::today();
        $end   = $start->copy()->addMonth();
    
        $allGames = [];
    
        $period = new \DatePeriod(
            $start,
            new \DateInterval('P1D'),
            $end->copy()->addDay()
        );
    
        foreach ($period as $date) {
            $ymd = $date->format('Ymd');
    
            $dayBlocks = Cache::remember("nba:schedule:$ymd", 3600, function () use ($ymd) {
                $response = $this->request('/nba-schedule-by-date', ['date' => $ymd]);
                return $response['response'] ?? [];
            });
    
            if (empty($dayBlocks)) {
                continue;
            }
    
            // Loop each "day block"
            foreach ($dayBlocks as $dayBlock) {
                if (!is_array($dayBlock)) {
                    continue;
                }
    
                $dayLabel = $dayBlock['scheduleDate'] ?? $ymd;
    
                // Loop each game inside the block
                foreach ($dayBlock as $key => $game) {
                    if (!is_int($key) || !is_array($game)) {
                        continue;
                    }
    
                    if (empty($game['competitors'])) {
                        continue;
                    }
    
                    $home = null;
                    $away = null;
                    
                    foreach ($game['competitors'] as $team) {
                        if (!empty($team['isHome']) && $team['isHome'] === true) {
                            $home = $team;
                        } else {
                            $away = $team;
                        }
                    }
                    
                    $allGames[] = [
                        'id'          => $game['id'] ?? null,
                        'scheduleDate'=> $dayLabel,
                        'tipoff'      => $game['date'] ?? null,
                        'status'      => $game['status']['detail'] ?? '',
                        'venue'       => $game['venue']['fullName'] ?? '',
                        'city'        => $game['venue']['address']['city'] ?? '',
                        'homeTeam'    => [
                            'id'    => $home['id'] ?? null,
                            'name'  => $home['displayName'] ?? '',
                            'short' => $home['shortDisplayName'] ?? '',
                            'logo'  => $home['logo'] ?? '',
                        ],
                        'awayTeam'    => [
                            'id'    => $away['id'] ?? null,
                            'name'  => $away['displayName'] ?? '',
                            'short' => $away['shortDisplayName'] ?? '',
                            'logo'  => $away['logo'] ?? '',
                        ],
                    ];
                    
                }
            }
        }

    
        return $allGames;
    }
    
    
    



    

}
