<?php

namespace App\View\Pages\Tenant\Backend\Hub;

use App\Models\Hub;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.templates.admin')]
class Show extends Component
{

    public Hub $hub;

    public function mount(Hub $hub)
    {
        $this->hub= $hub;
    }


    public function render()
    {
        return view('pages.tenant.backend.hub.show');
    }
}
