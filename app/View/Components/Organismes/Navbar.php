<?php

namespace App\View\Components\Organismes;

use Closure;
use Illuminate\Contracts\View\View;
use Livewire\Component;

// use Illuminate\View\Component;

class Navbar extends Component
{
    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.organismes.navbar');
    }
}
