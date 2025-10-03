<?php

namespace App\Http\Controllers\Lbs\Teams;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\League;
use App\Models\Team;

class TeamController extends Controller
{
    // Team detail (overview-like)
    public function show($id)
    {
        $team = Team::with(['players.games'])->findOrFail($id);

        $games = Game::where('team1_id', $team->id)
            ->orWhere('team2_id', $team->id)
            ->get();

        $gamesCount = max($games->count(), 1);

        $totalStats = ['points'=>0,'reb'=>0,'ast'=>0,'stl'=>0,'blk'=>0];

        foreach ($team->players as $player) {
            if (!$player->games) continue;
            foreach ($player->games as $game) {
                if (!$game->pivot) continue;
                if ($game->team1_id == $team->id || $game->team2_id == $team->id) {
                    $totalStats['points'] += $game->pivot->points ?? 0;
                    $totalStats['reb']    += $game->pivot->reb ?? 0;
                    $totalStats['ast']    += $game->pivot->ast ?? 0;
                    $totalStats['stl']    += $game->pivot->stl ?? 0;
                    $totalStats['blk']    += $game->pivot->blk ?? 0;
                }
            }
        }

        $averageStats = [
            'points' => $totalStats['points'] / $gamesCount,
            'reb'    => $totalStats['reb'] / $gamesCount,
            'ast'    => $totalStats['ast'] / $gamesCount,
            'stl'    => $totalStats['stl'] / $gamesCount,
            'blk'    => $totalStats['blk'] / $gamesCount,
        ];

        $bestPlayers = ['points'=>null,'rebounds'=>null,'assists'=>null];

        foreach (['points','reb','ast'] as $stat) {
            $bestPlayer = $team->players
                ->filter(fn($p) => $p->games && $p->games->count() > 0)
                ->map(fn($p) => (object)[
                    'id'    => $p->id,
                    'name'  => $p->name,
                    'value' => $p->games->sum(fn($g) => $g->pivot->{$stat} ?? 0),
                ])
                ->sortByDesc('value')
                ->first();

            if ($stat === 'reb')      $bestPlayers['rebounds'] = $bestPlayer;
            elseif ($stat === 'ast')  $bestPlayers['assists']  = $bestPlayer;
            else                      $bestPlayers['points']   = $bestPlayer;
        }

        $wins = $games->where('winner_id', $team->id)->count();
        $losses = $games->count() - $wins;

        // NEW view path:
        return view('lbs.teams.show', compact('team', 'averageStats', 'bestPlayers', 'wins', 'losses'));
    }

    public function games($id)
    {
        $team = Team::findOrFail($id);
        $parentLeagues = League::whereNull('parent_id')->get();
    
        $games = Game::where('team1_id', $team->id)
            ->orWhere('team2_id', $team->id)
            ->with(['team1', 'team2'])
            ->get()
            ->map(function ($game) {
                $game->score1 = $game->score2 = 0;
    
                if ($game->score) {
                    if (str_contains($game->score, '-')) {
                        $parts = explode('-', $game->score);
                    } elseif (str_contains($game->score, ':')) {
                        $parts = explode(':', $game->score);
                    } else {
                        $parts = [];
                    }
                    $game->score1 = isset($parts[0]) ? (int)$parts[0] : 0;
                    $game->score2 = isset($parts[1]) ? (int)$parts[1] : 0;
                } else {
                    $game->score1 = ($game->team1_q1 ?? 0) + ($game->team1_q2 ?? 0) + ($game->team1_q3 ?? 0) + ($game->team1_q4 ?? 0);
                    $game->score2 = ($game->team2_q1 ?? 0) + ($game->team2_q2 ?? 0) + ($game->team2_q3 ?? 0) + ($game->team2_q4 ?? 0);
                }
    
                return $game;
            });
    
        // Split by date
        $upcomingGames = $games->filter(fn($g) => $g->date && $g->date->isFuture())
            ->sortBy('date')->values();
    
        $pastGames = $games->filter(fn($g) => $g->date && ($g->date->isPast() || $g->date->isToday()))
            ->values()
            // Flag win/loss for this team (use recorded winner_id when present; else derive from score)
            ->map(function ($g) use ($team) {
                $winnerId = $g->winner_id;
                if (!$winnerId && ($g->score1 !== null && $g->score2 !== null)) {
                    if ($g->score1 > $g->score2) {
                        $winnerId = $g->team1_id;
                    } elseif ($g->score2 > $g->score1) {
                        $winnerId = $g->team2_id;
                    }
                }
                $g->is_win  = $winnerId && ((int)$winnerId === (int)$team->id);
                $g->is_loss = $winnerId && !$g->is_win;
                return $g;
            })
            ->sortByDesc('date')
            ->values();
    
        return view('lbs.teams.games', compact(
            'team',
            'parentLeagues',
            'games',
            'upcomingGames',
            'pastGames'
        ));
    }

    public function players($id)
    {
        $team = Team::with('players')->findOrFail($id);
        // NEW view path:
        return view('lbs.teams.players', compact('team'));
    }

    public function stats($id)
    {
        $team = Team::with(['players.games'])->findOrFail($id);

        $games = Game::where('team1_id', $team->id)
            ->orWhere('team2_id', $team->id)
            ->get();

        $wins = $games->where('winner_id', $team->id)->count();
        $losses = $games->count() - $wins;

        $totalGames = $games->count() ?: 1;

        $statKeys = [
            'points' => 'Punkti',
            'oreb'   => 'Atl. bumbas uzbrukumā',
            'dreb'   => 'Atl. bumbas aizsardzībā',
            'reb'    => 'Atl. bumbas',
            'ast'    => 'Piespēles',
            'pf'     => 'Fouls',
            'tov'    => 'Kļūdas',
            'stl'    => 'Pārķertās',
            'blk'    => 'Bloķētie metieni',
            'eff'    => 'effektivitāte'
        ];

        $averageStats = [];
        foreach ($statKeys as $key => $label) {
            $total = $team->players->sum(fn($p) => $p->games->sum("pivot.$key"));
            $averageStats[$key] = [
                'label' => $label,
                'avg'   => $total / $totalGames,
            ];
        }

        $playersStats = $team->players->map(function ($player) {
            $gamesPlayed = $player->games->count() ?: 1;
            return [
                'id' => $player->id,
                'name' => $player->name,
                'photo' => $player->photo,
                'gamesPlayed' => $gamesPlayed,
                'ppg' => $player->games->sum('pivot.points') / $gamesPlayed,
                'rpg' => $player->games->sum('pivot.reb') / $gamesPlayed,
                'apg' => $player->games->sum('pivot.ast') / $gamesPlayed,
                'minutes' => $player->games->sum(fn($g) => strtotime($g->pivot->minutes) - strtotime('00:00')) / $gamesPlayed,
            ];
        });

        // NEW view path:
        return view('lbs.teams.stats', compact(
            'team',
            'games',
            'wins',
            'losses',
            'averageStats',
            'playersStats'
        ));
    }
}
