<?php

namespace App\View\Pages\Central\Backend;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.templates.admin')]
class Dashboard extends Component
{
    public function render()
    {
        return view('pages.central.backend.dashboard');
    }
}
