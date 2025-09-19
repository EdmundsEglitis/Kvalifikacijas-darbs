<?php

namespace App\Http\Controllers;

use App\Services\NbaService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Carbon\Carbon;
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

    public function allPlayers(Request $request)
    {
        $page    = max((int) $request->query('page', 1), 1);
        $perPage = min(max((int) $request->query('perPage', 50), 10), 200);
        $q       = trim((string) $request->query('q', ''));
        $sort    = (string) $request->query('sort', 'name'); // name, team, height, weight
        $dir     = strtolower((string) $request->query('dir', 'asc')) === 'desc' ? 'desc' : 'asc';

        $players = collect($this->nba->allPlayersFromLoop());

        // Search by name or team
        if ($q !== '') {
            $qLower = Str::lower($q);
            $players = $players->filter(function ($p) use ($qLower) {
                $name = Str::lower(trim(($p['firstName'] ?? '') . ' ' . ($p['lastName'] ?? '')));
                $team = Str::lower((string) ($p['teamName'] ?? ''));
                return Str::contains($name, $qLower) || Str::contains($team, $qLower);
            });
        }

        // Helpers for parsing numeric sorts
        $parseWeight = function ($w) {
            // handles "220 lbs" or "100 kg" or plain number
            if (!is_string($w) && !is_numeric($w)) return null;
            $s = (string) $w;
            if (preg_match('/([\d\.]+)/', $s, $m)) {
                return (float) $m[1];
            }
            return null;
        };
        $parseHeight = function ($h) {
            // handles "6' 8\"" or "6-8" or "203 cm"
            if (!is_string($h)) return null;
            $s = trim($h);
            if (preg_match('/(\d+)\s*\'\s*(\d+)\s*"/', $s, $m)) {
                return (int)$m[1] * 12 + (int)$m[2];
            }
            if (preg_match('/(\d+)\s*-\s*(\d+)/', $s, $m)) {
                return (int)$m[1] * 12 + (int)$m[2];
            }
            if (preg_match('/([\d\.]+)\s*cm/i', $s, $m)) {
                return round(((float)$m[1]) / 2.54, 1);
            }
            if (preg_match('/([\d\.]+)/', $s, $m)) {
                return (float)$m[1];
            }
            return null;
        };

        // Sorting
        $players = $players->sort(function ($a, $b) use ($sort, $dir, $parseWeight, $parseHeight) {
            $cmp = 0;
            switch ($sort) {
                case 'team':
                    $va = strtolower($a['teamName'] ?? '');
                    $vb = strtolower($b['teamName'] ?? '');
                    $cmp = $va <=> $vb;
                    break;
                case 'height':
                    $va = $parseHeight($a['displayHeight'] ?? null) ?? -INF;
                    $vb = $parseHeight($b['displayHeight'] ?? null) ?? -INF;
                    $cmp = $va <=> $vb;
                    break;
                case 'weight':
                    $va = $parseWeight($a['displayWeight'] ?? null) ?? -INF;
                    $vb = $parseWeight($b['displayWeight'] ?? null) ?? -INF;
                    $cmp = $va <=> $vb;
                    break;
                case 'name':
                default:
                    $na = strtolower(trim(($a['firstName'] ?? '') . ' ' . ($a['lastName'] ?? '')));
                    $nb = strtolower(trim(($b['firstName'] ?? '') . ' ' . ($b['lastName'] ?? '')));
                    $cmp = $na <=> $nb;
                    break;
            }
            return $dir === 'desc' ? -$cmp : $cmp;
        })->values();

        // Pagination
        $total = $players->count();
        $items = $players->slice(($page - 1) * $perPage, $perPage)->values();
        $paginator = new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => url()->current(), 'query' => $request->query()]
        );

        return view('nba.players', ['players' => $paginator]);
    }


    public function showTeam($id)
    {
        $teams = $this->nba->allTeams();
        $team = $teams[$id] ?? null;
    
        if (!$team) {
            abort(404, 'Team not found');
        }
    
        return view('nba.team_show', ['team' => $team]);
    }
    
    public function showPlayer($id)
    {
        $players = $this->nba->allPlayersFromLoop();
        $player = collect($players)->firstWhere('id', (string) $id);
    
        if (!$player) {
            abort(404, 'Player not found');
        }
    
        return view('nba.player_show', ['player' => $player]);
    }

    public function upcomingGames()
    {
        $games = $this->nba->upcomingGames();

        return view('nba.games', ['games' => $games]);
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
