<?php

namespace App\View\Components;

use Illuminate\View\Component;

class MatchStatus extends Component
{
    /**
     * Der Status des Matches.
     *
     * @var string
     */
    public $status;

    /**
     * Erstellt eine neue Komponente-Instanz.
     *
     * @param  string  $status
     * @return void
     */
    public function __construct($status)
    {
        $this->status = $status;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.match-status');
    }
}
