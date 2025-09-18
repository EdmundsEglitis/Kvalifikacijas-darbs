<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SubLeagueTabs extends Component
{
    public $parentLeagues;
    public $subLeague;

    public function __construct($parentLeagues = null, $subLeague = null)
    {
        $this->parentLeagues = $parentLeagues;
        $this->subLeague = $subLeague;
    }

    public function render()
    {
        return view('components.sub-league-tabs');
    }
}
