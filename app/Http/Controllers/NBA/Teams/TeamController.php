<?php

namespace App\Http\Controllers\Nba\Teams;

use App\Http\Controllers\Controller;
use App\Models\NbaTeam;
use App\Models\NbaPlayer;
use App\Models\NbaGame;
use App\Models\NbaStanding;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    // /nba/teams (index)
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

        // NEW view path
        return view('nba.teams.index', compact('teams', 'q'));
    }

    // /nba/teams/{external_id}
    public function show($external_id)
    {
        $team = NbaTeam::where('external_id', $external_id)->firstOrFail();

        $players = NbaPlayer::where('team_id', $external_id)
            ->orderBy('full_name')->get();

        $games = NbaGame::where('home_team_id', $external_id)
            ->orWhere('away_team_id', $external_id)
            ->orderBy('tipoff')->get();

        $standing = NbaStanding::where('team_id', $external_id)
            ->orderByDesc('season')->first();

        $standingsHistory = NbaStanding::where('team_id', $external_id)
            ->where('season', '>=', 2021)
            ->orderByDesc('season')->get();

        // NEW view path
        return view('nba.teams.show', compact('team','players','games','standing','standingsHistory'));
    }
}
