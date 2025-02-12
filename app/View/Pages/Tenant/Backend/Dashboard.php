<?php

namespace App\View\Pages\Tenant\Backend;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.templates.admin')]
class Dashboard extends Component
{
    public function render()
    {
        return view('pages.tenant.backend.dashboard');
    }
}
