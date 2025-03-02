<?php

namespace App\View\Pages\Central\Backend\Product;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.templates.admin')]
class Index extends Component
{
    public function render()
    {
        return view('pages.central.backend.product.index');
    }
}
