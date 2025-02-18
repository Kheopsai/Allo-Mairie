<?php

namespace App\View\Pages\Tenant\Backend\Chat;

use App\Traits\HasChat;
use App\Traits\HasKheops;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.templates.admin')]
class Index extends Component
{
    use HasKheops;
    use HasChat;

    public bool $editable;

    /**
     * @throws Exception
     */
    public function create(): void
    {
        // $this->authorizeChannelOwner();
        $this->sendMessage();
        $this->createMessage();
    }



    public function render()
    {
        return view('pages.tenant.backend.chat.index');
    }
}
