<?php

namespace App\Http\Controllers;

use App\Models\League;
use App\Models\Team;
use App\Models\Game;
use Illuminate\Http\Request;

class LbsController extends Controller
{
    public function home()
    {
        return view('lbs.home'); // $parentLeagues automatically injected
    }

    public function lblLbsl()
    {
        return view('lbs.lbl-lbsl');
    }

    public function ljbl()
    {
        return view('lbs.ljbl');
    }

    public function izlases()
    {
        return view('lbs.izlases');
    }

    public function regionalieTurniri()
    {
        return view('lbs.regionalie-turniri');
    }

    public function showParent($id)
    {
        $parent = League::with('children')->findOrFail($id);

        return view('lbs.sub_leagues', [
            'parent' => $parent,
            'subLeagues' => $parent->children,
        ]);
    }

    public function showSubLeague($id)
    {
        $subLeague = League::findOrFail($id);
        return view('lbs.sub_league_detail', compact('subLeague'));
    }

    public function showTeams($id)
    {
        $subLeague = League::with('teams')->findOrFail($id);
        return view('lbs.subleague_teams', [
            'subLeague' => $subLeague,
            'teams' => $subLeague->teams,
        ]);
    }

    public function showTeam($id)
    {
        $team = Team::with(['players.games'])->findOrFail($id);
    
        $games = Game::where('team1_id', $team->id)
                     ->orWhere('team2_id', $team->id)
                     ->get();
    
        $gamesCount = max($games->count(), 1); // avoid division by zero
    
        // Sum stats from all players across all games
        $totalStats = [
            'points' => 0,
            'reb'    => 0,
            'ast'    => 0,
            'stl'    => 0,
            'blk'    => 0,
        ];
    
        foreach ($team->players as $player) {
            if (!$player->games) continue; // safety
            foreach ($player->games as $game) {
                if (!$game->pivot) continue; // safety
                if ($game->team1_id == $team->id || $game->team2_id == $team->id) {
                    $totalStats['points'] += $game->pivot->points ?? 0;
                    $totalStats['reb']    += $game->pivot->reb ?? 0;
                    $totalStats['ast']    += $game->pivot->ast ?? 0;
                    $totalStats['stl']    += $game->pivot->stl ?? 0;
                    $totalStats['blk']    += $game->pivot->blk ?? 0;
                }
            }
        }
    
        // Calculate averages
        $averageStats = [
            'points' => $totalStats['points'] / $gamesCount,
            'reb'    => $totalStats['reb'] / $gamesCount,
            'ast'    => $totalStats['ast'] / $gamesCount,
            'stl'    => $totalStats['stl'] / $gamesCount,
            'blk'    => $totalStats['blk'] / $gamesCount,
        ];
    
        // Best players
        $bestPlayers = [
            'points' => null,
            'rebounds' => null,
            'assists' => null,
        ];
    
        foreach (['points', 'reb', 'ast'] as $stat) {
            $bestPlayer = $team->players
                ->filter(fn($p) => $p->games && $p->games->count() > 0)
                ->map(fn($p) => [
                    'name' => $p->name,
                    'value' => $p->games->sum(fn($g) => $g->pivot->{$stat} ?? 0),
                ])
                ->sortByDesc('value')
                ->first();
    
            if ($stat === 'reb') $bestPlayers['rebounds'] = $bestPlayer;
            elseif ($stat === 'ast') $bestPlayers['assists'] = $bestPlayer;
            else $bestPlayers['points'] = $bestPlayer;
        }
    
        $wins = $games->where('winner_id', $team->id)->count();
        $losses = $games->count() - $wins;
    
        return view('lbs.team_detail', compact('team', 'averageStats', 'bestPlayers', 'wins', 'losses'));
    }
    

    public function teamGames($id)
    {
        $team = Team::findOrFail($id);

        $games = Game::where('team1_id', $team->id)
            ->orWhere('team2_id', $team->id)
            ->with(['team1', 'team2'])
            ->get()
            ->map(function ($game) {
                if ($game->score) {
                    [$game->score1, $game->score2] = explode('-', $game->score);
                } else {
                    $game->score1 = ($game->team1_q1 ?? 0) + ($game->team1_q2 ?? 0) + ($game->team1_q3 ?? 0) + ($game->team1_q4 ?? 0);
                    $game->score2 = ($game->team2_q1 ?? 0) + ($game->team2_q2 ?? 0) + ($game->team2_q3 ?? 0) + ($game->team2_q4 ?? 0);
                }
                return $game;
            });

        return view('lbs.team_games', compact('team', 'games'));
    }

    public function teamPlayers($id)
    {
        $team = Team::with('players')->findOrFail($id);
        return view('lbs.team_players', compact('team'));
    }

    public function teamStats($id)
    {
        $team = Team::findOrFail($id);
        $games = Game::where('team1_id', $team->id)
            ->orWhere('team2_id', $team->id)
            ->get();

        $wins = $games->where('winner_id', $team->id)->count();
        $losses = $games->count() - $wins;

        return view('lbs.team_stats', compact('team', 'games', 'wins', 'losses'));
    }

    public function showGame($id)
    {
        $game = Game::with([
            'team1', 
            'team2',
            'playerGameStats.player'
        ])->findOrFail($id);

        $team1Score = $team2Score = null;
        if ($game->score) {
            [$team1Score, $team2Score] = explode('-', $game->score);
        }

        $playerStats = $game->playerGameStats->groupBy('team_id');

        return view('lbs.game_detail', compact('game', 'team1Score', 'team2Score', 'playerStats'));
    }

    public function subLeagueNews($id)
    {
        $subLeague = League::findOrFail($id);
    
        // Get parent leagues for navbar
        $parentLeagues = League::whereNull('parent_id')->get();
    
        // Example news data – you can replace with real DB query
        $news = collect([
            (object)[
                'title' => 'Pirmā spēle aizvadīta',
                'content' => 'Komandas uzsāka sezonu ar aizraujošu spēli.',
                'created_at' => now()->subDays(2),
            ],
            (object)[
                'title' => 'Jauns līderis punktu guvējos',
                'content' => 'Spēlētājs X guva 30 punktus spēlē.',
                'created_at' => now()->subDay(),
            ],
        ]);
    
        return view('lbs.subleague_news', compact('subLeague', 'parentLeagues', 'news'));
    }
    

    public function subleagueCalendar($id)
    {
        $subLeague = League::with('teams')->findOrFail($id);
        $parentLeagues = League::whereNull('parent_id')->get();
    
        // Get all games for this sub-league
        $teamIds = $subLeague->teams->pluck('id');
        $games = Game::whereIn('team1_id', $teamIds)
                     ->orWhereIn('team2_id', $teamIds)
                     ->with(['team1', 'team2'])
                     ->orderBy('date', 'asc')
                     ->get();
    
        return view('lbs.subleague_calendar', compact('subLeague', 'parentLeagues', 'games'));
    }
    
    public function subleagueStats($id)
    {
        $subLeague = League::with('teams.players.playerGameStats')->findOrFail($id);
        $parentLeagues = League::whereNull('parent_id')->get();
    
        // Aggregate stats by team
        $teamsStats = $subLeague->teams->map(function($team){
            $games = Game::where('team1_id', $team->id)
                         ->orWhere('team2_id', $team->id)
                         ->get();
    
            $wins = $games->where('winner_id', $team->id)->count();
            $losses = $games->count() - $wins;
    
            return [
                'team' => $team,
                'wins' => $wins,
                'losses' => $losses,
            ];
        });
    
        return view('lbs.subleague_stats', compact('subLeague', 'parentLeagues', 'teamsStats'));
    }
    
}
