<?php

namespace App\View\Components\Flux;

use Illuminate\View\Component;

class Dialog extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        public bool $open = false,
        public ?string $id = null,
        public string $variant = 'default'
    ) {}

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.flux.dialog');
    }
}