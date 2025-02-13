<?php

namespace App\View\Pages\Tenant\Backend\Source;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.templates.admin')]
class Index extends Component
{
    public function render()
    {
        return view('pages.tenant.backend.source.index');
    }
}
