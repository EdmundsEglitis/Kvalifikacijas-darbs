<?php

namespace App\Http\Controllers\Nba\Games;

use App\Http\Controllers\Controller;
use App\Models\NbaGame;
use App\Services\NbaService;
use Carbon\Carbon;

class GameController extends Controller
{
    public function __construct(private NbaService $nba) {}

    // /nba/games  (upcoming)
    public function upcoming()
    {
        $games = NbaGame::query()
            ->where('tipoff', '>=', Carbon::now())
            ->orderBy('tipoff')
            ->take(20)
            ->get();

        // NEW view path
        return view('nba.games.index', compact('games'));
    }

    // /nba/all-games
    public function all()
    {
        $games = $this->nba->allGames();
        // NEW view path
        return view('nba.games.all', ['games' => $games['response'] ?? []]);
    }

    // /nba/games/{id}
    public function show($id)
    {
        $game = $this->nba->showGame($id);
        // NEW view path
        return view('nba.games.show', ['game' => $game['response'][0] ?? null]);
    }
}
