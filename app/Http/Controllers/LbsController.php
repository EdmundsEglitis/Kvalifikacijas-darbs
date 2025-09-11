<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Models\Player;
use App\Models\PlayerGameStat;
use App\Models\League;
use App\Models\Team;
use App\Models\Game;
use App\Models\News;
use Illuminate\Http\Request;

class LbsController extends Controller
{

    public function home()
    {
        $parentLeagues = League::whereNull('parent_id')->get();
        $news = News::latest()->take(6)->get(); // show latest 6 news items
    
        return view('lbs.home', compact('parentLeagues', 'news'));
    }
    
    public function showNews($id)
{
    $news = \App\Models\News::with('league')->findOrFail($id);
    $parentLeagues = \App\Models\League::whereNull('parent_id')->get();

    return view('lbs.news_detail', compact('news', 'parentLeagues'));
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
    
        $parentLeagues = League::whereNull('parent_id')->get(); // Pass for navbar
    
        $games = Game::where('team1_id', $team->id)
            ->orWhere('team2_id', $team->id)
            ->with(['team1', 'team2'])
            ->get()
            ->map(function ($game) {
                // Default scores
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
                    // Fallback: sum quarters
                    $game->score1 = ($game->team1_q1 ?? 0) + ($game->team1_q2 ?? 0) + ($game->team1_q3 ?? 0) + ($game->team1_q4 ?? 0);
                    $game->score2 = ($game->team2_q1 ?? 0) + ($game->team2_q2 ?? 0) + ($game->team2_q3 ?? 0) + ($game->team2_q4 ?? 0);
                }
    
                return $game;
            });
    
        return view('lbs.team_games', compact('team', 'games', 'parentLeagues'));
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
    
        $team1Score = $team2Score = 0; // default
    
        if ($game->score) {
            // support both "X-Y" or "X:Y" formats
            if (str_contains($game->score, '-')) {
                $parts = explode('-', $game->score);
            } elseif (str_contains($game->score, ':')) {
                $parts = explode(':', $game->score);
            } else {
                $parts = [];
            }
    
            $team1Score = isset($parts[0]) ? (int)$parts[0] : 0;
            $team2Score = isset($parts[1]) ? (int)$parts[1] : 0;
        } else {
            // fallback: sum quarter scores if available
            $team1Score = ($game->team1_q1 ?? 0) + ($game->team1_q2 ?? 0) + ($game->team1_q3 ?? 0) + ($game->team1_q4 ?? 0);
            $team2Score = ($game->team2_q1 ?? 0) + ($game->team2_q2 ?? 0) + ($game->team2_q3 ?? 0) + ($game->team2_q4 ?? 0);
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
        // Load subleague and parent leagues for navbars
        $subLeague = League::with('teams')->findOrFail($id);
        $parentLeagues = League::whereNull('parent_id')->get();
    
        // team ids in this sub-league
        $teamIds = $subLeague->teams->pluck('id')->toArray();
    
        // ---------------------
        // 1) Teams stats (wins / losses / games)
        // ---------------------
        $teamsStats = $subLeague->teams->map(function (Team $team) {
            $games = Game::where('team1_id', $team->id)
                         ->orWhere('team2_id', $team->id)
                         ->get();
    
            $wins = $games->where('winner_id', $team->id)->count();
            $losses = $games->count() - $wins;
    
            return [
                'team' => $team,
                'wins' => $wins,
                'losses' => $losses,
                'games_played' => $games->count(),
            ];
        });
    
        // ---------------------
        // 2) Players aggregated stats (AVG per game) for players that played for teams in this sub-league
        // We aggregate directly from player_game_stats to avoid depending on Player->games relation.
        // ---------------------
        if (empty($teamIds)) {
            $playersAgg = collect();
        } else {
            $playersAgg = DB::table('player_game_stats')
                ->select(
                    'player_id',
                    DB::raw('COUNT(*) as games_played'),
                    DB::raw('AVG(points) as avg_points'),
                    DB::raw('AVG(reb) as avg_reb'),
                    DB::raw('AVG(ast) as avg_ast'),
                    DB::raw('AVG(stl) as avg_stl'),
                    DB::raw('AVG(blk) as avg_blk'),
                    DB::raw('AVG(eff) as avg_eff')
                )
                ->whereIn('team_id', $teamIds)
                ->groupBy('player_id')
                ->get();
        }
    
        // preload Player models to avoid N+1
        $playerIds = $playersAgg->pluck('player_id')->unique()->filter()->values()->all();
        $playersById = Player::whereIn('id', $playerIds)
            ->with('team') // optional: will eager load team if relation exists
            ->get()
            ->keyBy('id');
    
        // Build playersStats collection used by the "All players" table
        $playersStats = $playersAgg->map(function ($row) use ($playersById) {
            $player = $playersById->get($row->player_id);
    
            return (object) [
                'id' => $row->player_id,
                'name' => $player ? $player->name : ('Player #' . $row->player_id),
                'team' => $player && $player->team ? $player->team : null,
                'games' => (int) $row->games_played,
                'avg_points' => $row->avg_points !== null ? round($row->avg_points, 1) : 0,
                'avg_rebounds' => $row->avg_reb !== null ? round($row->avg_reb, 1) : 0,
                'avg_assists' => $row->avg_ast !== null ? round($row->avg_ast, 1) : 0,
                'avg_steals' => $row->avg_stl !== null ? round($row->avg_stl, 1) : 0,
                'avg_blocks' => $row->avg_blk !== null ? round($row->avg_blk, 1) : 0,
                'avg_eff' => $row->avg_eff !== null ? round($row->avg_eff, 1) : 0,
            ];
        });
    
        // ---------------------
        // 3) Top players per stat (highest AVG)
        // ---------------------
        $topPlayers = collect();
    
        if ($playersAgg->isNotEmpty()) {
            // convert to collection keyed by player_id for easy lookup
            $aggCollection = $playersAgg->mapWithKeys(fn($r) => [$r->player_id => $r]);
    
            // helper to pick best by column name
            $pickBest = function ($col) use ($aggCollection, $playersById) {
                $best = $aggCollection->sortByDesc($col)->first();
                if (!$best) {
                    return null;
                }
                $player = $playersById->get($best->player_id);
                return (object) [
                    'id' => $best->player_id,
                    'name' => $player ? $player->name : ('Player #' . $best->player_id),
                    'team' => $player && $player->team ? $player->team : null,
                    'avg_value' => $best->$col !== null ? round($best->$col, 1) : 0,
                ];
            };
    
            $topPlayers['points']   = $pickBest('avg_points');
            $topPlayers['rebounds'] = $pickBest('avg_reb');
            $topPlayers['assists']  = $pickBest('avg_ast');
            $topPlayers['steals']   = $pickBest('avg_stl');
            $topPlayers['blocks']   = $pickBest('avg_blk');
            $topPlayers['eff']      = $pickBest('avg_eff');
        }
    
        // sort teamsStats by wins descending so the best is first (view can also do this)
        $teamsStats = $teamsStats->sortByDesc(fn($t) => $t['wins'])->values();
    
        // Pass everything to the view
        return view('lbs.subleague_stats', [
            'subLeague' => $subLeague,
            'parentLeagues' => $parentLeagues,
            'teamsStats' => $teamsStats,
            'topPlayers' => $topPlayers,
            'playersStats' => $playersStats,
        ]);
    }
    
    
}
