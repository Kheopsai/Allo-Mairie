<?php

namespace App\View\Pages\Central\Backend\User;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.templates.admin')]
class Index extends Component
{
    public function render()
    {
        return view('pages.central.backend.user.index');
    }
}
