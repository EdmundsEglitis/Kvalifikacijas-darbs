<?php

namespace App\Http\Controllers;
use App\Models\League;
use Illuminate\Http\Request;

class LbsController extends Controller
{
    public function home()
    {
               // Fetch all parent leagues (no parent_id)
               $parentLeagues = League::whereNull('parent_id')->get();

               return view('lbs.home', compact('parentLeagues'));
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
    
        return view('lbs.sub_leagues', [ // <-- use 'sub_leagues' instead of 'parent'
            'parent' => $parent,
            'subLeagues' => $parent->children,
        ]);
    }
    public function showSubLeague($id)
{
    $subLeague = League::findOrFail($id);

    return view('lbs.sub_league_detail', [
        'subLeague' => $subLeague,
    ]);
}
public function showTeams($id)
{
    $subLeague = League::with('teams')->findOrFail($id);

    return view('lbs.subleague_teams', [
        'subLeague' => $subLeague,
        'teams' => $subLeague->teams,
    ]);
}

    
}
