<?php

namespace App\Http\Controllers\Nba;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Controller;
use App\Models\NbaGame;
use App\Models\NbaPlayerGameLog;
use App\Models\NbaStanding;
use App\Models\NbaTeam;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function home()
    {
        // Next 6 upcoming games
        $upcomingGames = NbaGame::query()
            ->where('tipoff', '>=', Carbon::now())
            ->orderBy('tipoff')
            ->take(6)
            ->get();

        // Fill missing team logos/names from teams table (so cards always have icons)
        if ($upcomingGames->isNotEmpty()) {
            $teamIds = $upcomingGames
                ->pluck('home_team_id')
                ->merge($upcomingGames->pluck('away_team_id'))
                ->filter()
                ->unique()
                ->values();

            $teams = NbaTeam::whereIn('external_id', $teamIds)->get()
                ->keyBy('external_id');

            foreach ($upcomingGames as $g) {
                if ((!$g->home_team_logo || !$g->home_team_name) && $g->home_team_id && isset($teams[$g->home_team_id])) {
                    $t = $teams[$g->home_team_id];
                    $g->home_team_logo = $g->home_team_logo ?: $t->logo;
                    $g->home_team_name = $g->home_team_name ?: $t->name;
                    $g->home_team_short = $g->home_team_short ?: $t->abbreviation;
                }
                if ((!$g->away_team_logo || !$g->away_team_name) && $g->away_team_id && isset($teams[$g->away_team_id])) {
                    $t = $teams[$g->away_team_id];
                    $g->away_team_logo = $g->away_team_logo ?: $t->logo;
                    $g->away_team_name = $g->away_team_name ?: $t->name;
                    $g->away_team_short = $g->away_team_short ?: $t->abbreviation;
                }
            }
        }

        $currentYear = (int) date('Y');

        $query = NbaPlayerGameLog::query()
            ->from('nba_player_game_logs as l')
            ->select([
                'l.player_external_id',
                DB::raw('AVG(l.points) as ppg'),
                DB::raw('COUNT(*) as g'),
            ])
            ->whereYear('l.game_date', $currentYear)
            ->groupBy('l.player_external_id')
            ->orderByDesc('ppg')
            ->take(6);
        
        // Join nba_players only if present, and add fields defensively
        if (Schema::hasTable('nba_players')) {
            $query->leftJoin('nba_players as p', 'p.external_id', '=', 'l.player_external_id');
        
            // Resolve a safe "name" expression
            $nameExpr = null;
            if (Schema::hasColumn('nba_players', 'name')) {
                $nameExpr = 'p.name';
            } elseif (Schema::hasColumn('nba_players', 'full_name')) {
                $nameExpr = 'p.full_name';
            } elseif (Schema::hasColumn('nba_players', 'display_name')) {
                $nameExpr = 'p.display_name';
            } elseif (Schema::hasColumn('nba_players', 'first_name') && Schema::hasColumn('nba_players', 'last_name')) {
                $nameExpr = "CONCAT(p.first_name,' ',p.last_name)";
            }
        
            // Resolve a safe "photo" coalesce
            $photoCols = array_values(array_filter([
                Schema::hasColumn('nba_players', 'photo')        ? 'p.photo'        : null,
                Schema::hasColumn('nba_players', 'photo_url')    ? 'p.photo_url'    : null,
                Schema::hasColumn('nba_players', 'headshot')     ? 'p.headshot'     : null,
                Schema::hasColumn('nba_players', 'headshot_url') ? 'p.headshot_url' : null,
                Schema::hasColumn('nba_players', 'image_url')    ? 'p.image_url'    : null,
            ]));
        
            if ($nameExpr) {
                $query->addSelect(DB::raw("$nameExpr as player_name"));
                $query->groupBy(DB::raw($nameExpr));
            } else {
                $query->addSelect(DB::raw('NULL as player_name'));
            }
        
            if (!empty($photoCols)) {
                $query->addSelect(DB::raw('COALESCE('.implode(',', $photoCols).') as player_photo'));
                foreach ($photoCols as $col) {
                    $query->groupBy(DB::raw($col));
                }
            } else {
                $query->addSelect(DB::raw('NULL as player_photo'));
            }
        } else {
            // nba_players table not present, keep fields nullable
            $query->addSelect(DB::raw('NULL as player_name'), DB::raw('NULL as player_photo'));
        }
        
        $topPpg = $query->get();

        // Standings snapshot: top 8 by win% (latest season)
        $latestSeason = NbaStanding::max('season');
        $standings = NbaStanding::query()
            ->where('season', $latestSeason)
            ->orderByDesc('win_percent')
            ->take(8)
            ->get();

        // Featured teams (12) — we’ll display logos inside a padded, object-contain canvas to avoid cropping
        $teams = NbaTeam::orderBy('name')->take(12)->get();

        return view('nba.home', compact(
            'upcomingGames',
            'topPpg',
            'standings',
            'latestSeason',
            'teams'
        ));
    }
}
