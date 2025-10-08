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
     * Die Variante für das Badge (bestimmt die Farbe).
     *
     * @var string
     */
    public $variant;

    /**
     * Erstellt eine neue Komponente-Instanz.
     *
     * @param  string  $status
     * @return void
     */
    public function __construct($status)
    {
        $this->status = $status;
        $this->variant = $this->getVariantFromStatus($status);
    }

    /**
     * Get the variant (color) based on the status.
     *
     * @param string $status
     * @return string
     */
    protected function getVariantFromStatus($status)
    {
        return match ($status) {
            'created' => 'gray',
            'in_progress' => 'amber',
            'submitted' => 'green',
            'closed' => 'blue',
            default => 'gray',
        };
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
