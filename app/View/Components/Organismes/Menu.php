<?php

namespace App\View\Components\Organismes;

use Closure;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Menu extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.organismes.menu');
    }
}
