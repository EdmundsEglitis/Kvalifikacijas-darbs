<?php
namespace App\Http\Controllers\Lbs;

use App\Http\Controllers\Controller;
use App\Models\HeroImage;
use App\Models\League;
use App\Models\News;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function home()
    {
        $parentLeagues = League::whereNull('parent_id')->get();

        $heroImage = HeroImage::whereNull('league_id')
            ->latest('created_at')
            ->first();

        $upcomingGames = DB::table('games as g')
            ->leftJoin('teams as h', 'h.id', '=', 'g.team1_id')
            ->leftJoin('teams as a', 'a.id', '=', 'g.team2_id')
            ->whereNotNull('g.date')
            ->where('g.date', '>', now())     
            ->orderBy('g.date')
            ->limit(24)                       
            ->get([
                'g.id',
                'g.date as tipoff',
                'h.name as home_team_name',
                'h.logo as home_team_logo',
                'a.name as away_team_name',
                'a.logo as away_team_logo',
            ])
            ->map(function ($row) {
                $toUrl = function ($p) {
                    if (!$p) return null;
                    return preg_match('~^https?://~i', $p) ? $p : asset('storage/' . ltrim($p, '/'));
                };
                $row->home_team_logo = $toUrl($row->home_team_logo);
                $row->away_team_logo = $toUrl($row->away_team_logo);
                return $row;
            });

        $slots = ['secondary-1','secondary-2','slot-1','slot-2','slot-3'];
        $bySlot = collect($slots)->mapWithKeys(function ($slot) {
            $item = News::where('position', $slot)->latest('created_at')->first();
            if (!$item) return [];
            $clean = preg_replace('/<figure.*?<\/figure>/is', '', $item->content);
            $item->excerpt = Str::limit(strip_tags($clean), 150, '…');

            libxml_use_internal_errors(true);
            $doc = new \DOMDocument();
            $doc->loadHTML('<?xml encoding="utf-8" ?>' . $item->content);
            libxml_clear_errors();
            $img = $doc->getElementsByTagName('img')->item(0);
            $item->preview_image = $img?->getAttribute('src');

            return [$slot => $item];
        });

        return view('lbs.home', compact('parentLeagues', 'heroImage', 'bySlot', 'upcomingGames'));
    }
}
