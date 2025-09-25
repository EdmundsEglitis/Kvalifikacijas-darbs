<?php

namespace App\Http\Controllers;
use App\Models\HeroImage;
use Illuminate\Support\Facades\DB;
use App\Models\Player;
use App\Models\PlayerGameStat;
use App\Models\League;
use App\Models\Team;
use App\Models\Game;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class LbsController extends Controller
{
    public function home()
    {
        $parentLeagues = League::whereNull('parent_id')->get();

        $heroImage = HeroImage::whereNull('league_id')
            ->latest('created_at')
            ->first();
    

        $slots = ['secondary-1','secondary-2','slot-1','slot-2','slot-3'];
        $bySlot = collect($slots)
            ->mapWithKeys(function ($slot) {
                $item = News::where('position', $slot)
                    ->latest('created_at')
                    ->first();
                if (! $item) {
                    return [];
                }

                $clean = preg_replace('/<figure.*?<\/figure>/is', '', $item->content);
                $item->excerpt = \Illuminate\Support\Str::limit(strip_tags($clean), 150, '…');
    
                libxml_use_internal_errors(true);
                $doc = new \DOMDocument();
                $doc->loadHTML('<?xml encoding="utf-8" ?>' . $item->content);
                libxml_clear_errors();
                $img = $doc->getElementsByTagName('img')->item(0);
                $item->preview_image = $img?->getAttribute('src');
    
                return [$slot => $item];
            });
    
        return view('lbs.home', compact('parentLeagues', 'heroImage', 'bySlot'));
    }
    
    public function showNews($id)
    {
        $news = \App\Models\News::with('league')->findOrFail($id);
        $parentLeagues = \App\Models\League::whereNull('parent_id')->get();
    
        $cleanContent = preg_replace('/<figcaption.*?<\/figcaption>/is', '', $news->content);
        $news->clean_content = $cleanContent;
    
        return view('lbs.news_detail', compact('news', 'parentLeagues'));
    }
    
    public function showParent($id)
    {
        $parent = League::with('children')->findOrFail($id);
        $subLeagues = $parent->children;
    
        $heroImage = \App\Models\HeroImage::where('league_id', $parent->id)
            ->latest('created_at')
            ->first();
    
        $news = \App\Models\News::whereIn('league_id', $subLeagues->pluck('id')->push($parent->id))
            ->latest()
            ->take(12)
            ->get()
            ->map(function ($item) {
                $clean = preg_replace('/<figure.*?<\/figure>/is', '', $item->content);
                $item->excerpt = \Illuminate\Support\Str::limit(strip_tags($clean), 150, '…');
    
                $item->preview_image = null;
                if (!empty(trim($item->content))) {
                    libxml_use_internal_errors(true);
                    $doc = new \DOMDocument();
                    $doc->loadHTML('<?xml encoding="utf-8" ?>' . $item->content);
                    libxml_clear_errors();
                    $imgNode = $doc->getElementsByTagName('img')->item(0);
                    if ($imgNode) {
                        $item->preview_image = $imgNode->getAttribute('src');
                    }
                }
    
                return $item;
            });
    
        return view('lbs.sub_leagues', compact('parent', 'subLeagues', 'heroImage', 'news'));
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
    
        $gamesCount = max($games->count(), 1); 

        $totalStats = [
            'points' => 0,
            'reb'    => 0,
            'ast'    => 0,
            'stl'    => 0,
            'blk'    => 0,
        ];
    
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
    

        $bestPlayers = [
            'points' => null,
            'rebounds' => null,
            'assists' => null,
        ];
    
        foreach (['points', 'reb', 'ast'] as $stat) {
            $bestPlayer = $team->players
                ->filter(fn($p) => $p->games && $p->games->count() > 0)
                ->map(function ($p) use ($stat) {
                    return (object)[
                        'id'    => $p->id,
                        'name'  => $p->name,
                        'value' => $p->games->sum(fn($g) => $g->pivot->{$stat} ?? 0),
                    ];
                })
                ->sortByDesc('value')
                ->first();
        
            if ($stat === 'reb') {
                $bestPlayers['rebounds'] = $bestPlayer;
            } elseif ($stat === 'ast') {
                $bestPlayers['assists'] = $bestPlayer;
            } else {
                $bestPlayers['points'] = $bestPlayer;
            }
        }
        
    
        $wins = $games->where('winner_id', $team->id)->count();
        $losses = $games->count() - $wins;
    
        return view('lbs.team_detail', compact('team', 'averageStats', 'bestPlayers', 'wins', 'losses'));
    }
    
public function teamGames($id)
{
    $team = Team::findOrFail($id);

    $parentLeagues = League::whereNull('parent_id')->get();

    // Fetch games where this team played
    $games = Game::where('team1_id', $team->id)
        ->orWhere('team2_id', $team->id)
        ->with(['team1', 'team2'])
        ->get()
        ->map(function ($game) {
            // Default
            $game->score1 = $game->score2 = 0;

            // If stored as "100-95" or "100:95"
            if ($game->score) {
                if (str_contains($game->score, '-')) {
                    $parts = explode('-', $game->score);
                } elseif (str_contains($game->score, ':')) {
                    $parts = explode(':', $game->score);
                } else {
                    $parts = [];
                }

                $game->score1 = isset($parts[0]) ? (int) $parts[0] : 0;
                $game->score2 = isset($parts[1]) ? (int) $parts[1] : 0;
            } else {
                // If no "score", sum quarter scores
                $game->score1 = ($game->team1_q1 ?? 0) + ($game->team1_q2 ?? 0) + ($game->team1_q3 ?? 0) + ($game->team1_q4 ?? 0);
                $game->score2 = ($game->team2_q1 ?? 0) + ($game->team2_q2 ?? 0) + ($game->team2_q3 ?? 0) + ($game->team2_q4 ?? 0);
            }

            return $game;
        });

    // Split into upcoming and past games
    $upcomingGames = $games->filter(fn($g) => $g->date && $g->date->isFuture())
                           ->sortBy('date')
                           ->values();

    $pastGames = $games->filter(fn($g) => $g->date && ($g->date->isPast() || $g->date->isToday()))
                       ->sortByDesc('date')
                       ->values();

    return view('lbs.team_games', compact(
        'team',
        'parentLeagues',
        'games',
        'upcomingGames',
        'pastGames'
    ));
}


    public function teamPlayers($id)
    {
        $team = Team::with('players')->findOrFail($id);
        return view('lbs.team_players', compact('team'));
    }

public function teamStats($id)
{
    $team = Team::with(['players.games'])->findOrFail($id);

    $games = Game::where('team1_id', $team->id)
        ->orWhere('team2_id', $team->id)
        ->get();

    $wins = $games->where('winner_id', $team->id)->count();
    $losses = $games->count() - $wins;

    $totalGames = $games->count() ?: 1;

    // Stats we want to calculate
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
        'dunk'   => 'Danki',
    ];

    // Calculate team averages
    $averageStats = [];
    foreach ($statKeys as $key => $label) {
        $total = $team->players->sum(fn($p) => $p->games->sum("pivot.$key"));
        $averageStats[$key] = [
            'label' => $label,
            'avg'   => $total / $totalGames,
        ];
    }

    // Player averages
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
            'minutes' => $player->games->sum(function ($g) {
                return strtotime($g->pivot->minutes) - strtotime('00:00');
            }) / $gamesPlayed,
        ];
    });

    return view('lbs.team_stats', compact(
        'team',
        'games',
        'wins',
        'losses',
        'averageStats',
        'playersStats'
    ));
}

public function showGame($id)
{
    $game = Game::with([
        'team1',
        'team2',
        'playerGameStats.player'
    ])->findOrFail($id);

    // Parse scores
    $team1Score = $team2Score = 0;

    if ($game->score) {
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
        $team1Score = ($game->team1_q1 ?? 0) + ($game->team1_q2 ?? 0) + ($game->team1_q3 ?? 0) + ($game->team1_q4 ?? 0);
        $team2Score = ($game->team2_q1 ?? 0) + ($game->team2_q2 ?? 0) + ($game->team2_q3 ?? 0) + ($game->team2_q4 ?? 0);
    }

    $playerStats = $game->playerGameStats->groupBy('team_id');

    $parentLeagues = League::whereNull('parent_id')->get();

    return view('lbs.game_detail', compact(
        'game',
        'team1Score',
        'team2Score',
        'playerStats',
        'parentLeagues'
    ));
}

    
    
    public function subLeagueNews($id)
    {
            $subLeague     = League::findOrFail($id);
            $parentLeagues = League::whereNull('parent_id')->get();
    
            $heroImage = HeroImage::where('league_id', $subLeague->id)
                ->latest('created_at')
                ->first();
    

            $slots  = ['secondary-1','secondary-2','slot-1','slot-2','slot-3'];
    

            $bySlot = collect($slots)->mapWithKeys(function (string $slot) use ($subLeague) {
                $item = News::where('league_id', $subLeague->id)
                    ->where('position', $slot)
                    ->latest('created_at')
                    ->first();
    
                if (! $item) {
                    return [];  
                }
    

                $clean = preg_replace('/<figure.*?<\/figure>/is', '', $item->content ?? '');
                $item->excerpt = Str::limit(strip_tags($clean), 150, '…');
    

                if (empty($item->preview_image)) {
                    libxml_use_internal_errors(true);
                    $doc = new \DOMDocument();
                    $doc->loadHTML('<?xml encoding="utf-8" ?>' . ($item->content ?? ''));
                    libxml_clear_errors();
                    $img = $doc->getElementsByTagName('img')->item(0);
                    $item->preview_image = $img?->getAttribute('src') ?: null;
                }
    
                return [$slot => $item];
            });
    
            return view('lbs.subleague_news', compact(
                'subLeague',
                'parentLeagues',
                'heroImage',
                'bySlot',
            ));
    }
    

    public function subleagueCalendar($id)
    {
        $subLeague     = League::with('teams')->findOrFail($id);
        $parentLeagues = League::whereNull('parent_id')->get();
    
        $teamIds = $subLeague->teams->pluck('id');
    
        $games = Game::whereIn('team1_id', $teamIds)
            ->orWhereIn('team2_id', $teamIds)
            ->with(['team1', 'team2', 'winner'])   // <-- add winner
            ->orderBy('date', 'asc')
            ->get();
    
        $upcomingGames = $games->filter(fn($g) => $g->date->isFuture());
        $pastGames     = $games->filter(fn($g) => $g->date->isPast());
    
        return view('lbs.subleague_calendar', compact(
            'subLeague',
            'parentLeagues',
            'games',
            'upcomingGames',
            'pastGames'
        ));
    }
    

    public function show($id)
    {
            $player = Player::with(['team.league', 'playerGameStats.game.team1', 'playerGameStats.game.team2'])
                ->findOrFail($id);
        
            $parentLeagues = League::whereNull('parent_id')->get();
        
            $stats = $player->playerGameStats;
            $totals = [
                'games'   => $stats->count(),
                'points'  => $stats->sum('points'),
                'reb'     => $stats->sum('reb'),
                'ast'     => $stats->sum('ast'),
                'stl'     => $stats->sum('stl'),
                'blk'     => $stats->sum('blk'),
                'eff'     => $stats->sum('eff'),
            ];
            $averages = $totals['games'] > 0 ? [
                'points' => round($totals['points'] / $totals['games'], 1),
                'reb'    => round($totals['reb'] / $totals['games'], 1),
                'ast'    => round($totals['ast'] / $totals['games'], 1),
                'stl'    => round($totals['stl'] / $totals['games'], 1),
                'blk'    => round($totals['blk'] / $totals['games'], 1),
                'eff'    => round($totals['eff'] / $totals['games'], 1),
            ] : [];
        
            return view('lbs.player_show', compact('player', 'parentLeagues', 'totals', 'averages'));
    }
        
    
    public function subleagueStats($id)
    {
        $subLeague = League::with('teams')->findOrFail($id);
        $parentLeagues = League::whereNull('parent_id')->get();
    
        $teamIds = $subLeague->teams->pluck('id')->toArray();
    
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

        $playerIds = $playersAgg->pluck('player_id')->unique()->filter()->values()->all();
        $playersById = Player::whereIn('id', $playerIds)
            ->with('team')
            ->get()
            ->keyBy('id');

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
        $topPlayers = collect();
    
        if ($playersAgg->isNotEmpty()) {
            $aggCollection = $playersAgg->mapWithKeys(fn($r) => [$r->player_id => $r]);
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
        $teamsStats = $teamsStats->sortByDesc(fn($t) => $t['wins'])->values();
        return view('lbs.subleague_stats', [
            'subLeague' => $subLeague,
            'parentLeagues' => $parentLeagues,
            'teamsStats' => $teamsStats,
            'topPlayers' => $topPlayers,
            'playersStats' => $playersStats,
        ]);
    }    
}
