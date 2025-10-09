<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class MatchListItem extends Component
{
    public $match;
    public $isInCreatorTeam;
    public $isInSolverTeam;
    public $showActions;

    /**
     * Create a new component instance.
     */
    public function __construct($match, $isInCreatorTeam = false, $isInSolverTeam = false, $showActions = true)
    {
        $this->match = $match;
        $this->isInCreatorTeam = $isInCreatorTeam;
        $this->isInSolverTeam = $isInSolverTeam;
        $this->showActions = $showActions;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.match-list-item');
    }
}
