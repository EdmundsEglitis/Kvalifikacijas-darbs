<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

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

    public function allPlayers($season = 2024)
    {
        return $this->request('/players', [
            'league' => 12, // 12 = NBA
            'season' => $season,
        ]);
    }

    public function upcomingGames($season = 2024)
    {
        return $this->request('/games', [
            'league' => 12,
            'season' => $season,
            'next' => 10,
        ]);
    }

    public function allGames($season = 2024)
    {
        return $this->request('/games', [
            'league' => 12,
            'season' => $season,
        ]);
    }

    public function showGame($id)
    {
        return $this->request('/games', [
            'id' => $id,
        ]);
    }
}
