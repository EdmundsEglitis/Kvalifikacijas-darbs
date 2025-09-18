<?php
namespace App\View\Components;

use Illuminate\View\Component;

class TeamNavbar extends Component
{
    public $parentLeagues;
    public $team;

    public function __construct($parentLeagues = null, $team = null)
    {
        $this->parentLeagues = $parentLeagues;
        $this->team = $team;
    }

    public function render()
    {
        return view('components.team-navbar');
    }
}
