<?php

namespace App\Http\Controllers\Nba\Teams;

use App\Http\Controllers\Controller;
use App\Models\NbaTeam;
use App\Models\NbaPlayer;
use App\Models\NbaGame;
use App\Models\NbaStanding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $teams = NbaTeam::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(fn($q2) => $q2
                    ->where('name', 'like', "%{$q}%")
                    ->orWhere('short_name', 'like', "%{$q}%")
                    ->orWhere('abbreviation', 'like', "%{$q}%")
                );
            })
            ->orderBy('name')
            ->paginate(31)
            ->withQueryString();

        return view('nba.teams.index', compact('teams', 'q'));
    }

    public function show($external_id)
    {
        $team = NbaTeam::where('external_id', $external_id)->firstOrFail();
    
        $players = NbaPlayer::where('team_id', $external_id)
            ->orderBy('full_name')->get();
    
        // Upcoming games from schedules
        $now = now();
        $upcomingGames = NbaGame::where(function ($q) use ($external_id) {
                $q->where('home_team_id', $external_id)
                  ->orWhere('away_team_id', $external_id);
            })
            ->whereNotNull('tipoff')
            ->where('tipoff', '>=', $now)
            ->orderBy('tipoff')
            ->get();
    
        // (Optional) Keep the original $games if another part of the page still needs all games
        $games = $upcomingGames;
    
        $standing = NbaStanding::where('team_id', $external_id)
            ->orderByDesc('season')->first();
    
        $standingsHistory = NbaStanding::where('team_id', $external_id)
            ->where('season', '>=', 2021)
            ->orderByDesc('season')->get();
    
        // Past games from logs (latest 15)
        $pastGames = DB::table('nba_player_game_logs as l')
            ->join('nba_players as p', 'p.external_id', '=', 'l.player_external_id')
            ->where('p.team_id', $external_id)
            ->whereNotNull('l.game_date')
            ->whereNotNull('l.score')
            ->selectRaw('
                l.event_id,
                MAX(l.game_date)           as game_date,
                /* If ANY_VALUE isn’t supported on your MySQL, replace with MIN() below */
                ANY_VALUE(l.opponent_name) as opponent_name,
                ANY_VALUE(l.opponent_logo) as opponent_logo,
                ANY_VALUE(l.result)        as result,
                ANY_VALUE(l.score)         as score
            ')
            ->groupBy('l.event_id')
            ->orderByDesc('game_date')
            ->limit(15)
            ->get();
    
        return view('nba.teams.show', compact(
            'team','players','games','upcomingGames','standing','standingsHistory','pastGames'
        ));
    }
    
}
