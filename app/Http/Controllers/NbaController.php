<?php

namespace App\Http\Controllers;
use App\Models\Game;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NbaController extends Controller  
{


    public function upcomingGames()
    {
        $games = [];
        $startDate = \Carbon\Carbon::parse('2025-10-21'); // NBA season start
        $daysToFetch = 1; // how many days ahead to check
    

            dd($response = Http::withHeaders([
                'x-rapidapi-host' => 'nba-api-free-data.p.rapidapi.com',
                'x-rapidapi-key'  => env('NBA_API_KEY')
            ])->get('https://nba-api-free-data.p.rapidapi.com/nba-schedule-by-date', [
                'date' => $startDate
            ]));
    
            $dayGames = $response->json()['response']['Games'] ?? []; // check API response structure
            $games = array_merge($games, $dayGames);
        
    
        // Optional: sort by game date & time
        usort($games, fn($a, $b) => strcmp($a['date'], $b['date']));
    
        return view('nba.upcoming', compact('games'));
    }
    
    public function allPlayers()
    {
        $players = [];
    
        // Loop through all NBA team IDs (1-30)
        for ($teamId = 1; $teamId <= 30; $teamId++) {
            $response = Http::withHeaders([
                'x-rapidapi-host' => 'nba-api-free-data.p.rapidapi.com',
                'x-rapidapi-key'  => env('NBA_API_KEY')
            ])->get('https://nba-api-free-data.p.rapidapi.com/nba-player-list', [
                'teamid' => $teamId
            ]);
    
            $teamPlayers = $response->json()['response']['PlayerList'] ?? [];
            $players = array_merge($players, $teamPlayers);
        }
    
        // Optional: sort players alphabetically by fullName
        usort($players, fn($a, $b) => strcmp($a['fullName'], $b['fullName']));
    
        return view('nba.players', compact('players'));
    }
    public function home()
    {
        return view('nba.home');
    }

public function allGames()
{
    // Eager load team1, team2, and winner
    $games = Game::with(['team1', 'team2', 'winner'])
                 ->orderBy('date', 'asc')
                 ->get();

    return view('nba.games', compact('games'));
}

    // Show a single game with player stats
    public function showGame($id)
    {
        $game = Game::with([
            'team1',
            'team2',
            'winner',
            'playerStats.player',
            'playerStats.team'
        ])->findOrFail($id);

        return view('nba.game_show', compact('game'));
    }
    
    
    
}
