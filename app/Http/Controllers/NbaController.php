<?php

namespace App\Http\Controllers;

use App\Services\NbaService;
use Illuminate\Http\Request;

class NbaController extends Controller
{
    protected NbaService $nba;

    public function __construct(NbaService $nba)
    {
        $this->nba = $nba;
    }

    public function home()
    {
        return view('nba.home');
    }

    public function allPlayers()
    {
        try {
            $players = $this->nba->allPlayers();
            return view('nba.players', ['players' => $players['response'] ?? []]);
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage());
        }
    }

    public function upcomingGames()
    {
        $games = $this->nba->upcomingGames();
        return view('nba.games', ['games' => $games['response'] ?? []]);
    }

    public function allGames()
    {
        $games = $this->nba->allGames();
        return view('nba.all_games', ['games' => $games['response'] ?? []]);
    }

    public function showGame($id)
    {
        $game = $this->nba->showGame($id);
        return view('nba.game_detail', ['game' => $game['response'][0] ?? null]);
    }
}
