<?php

namespace App\View\Pages\Tenant\Backend\Hub;

use App\Models\Hub;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

#[Layout('components.templates.admin')]
class Index extends Component
{
    use WireUiActions;

    public $hubs;

    public function mount()
    {
        $this->hubs = Hub::where('user_id',Auth::id())->get();
    }

    public function deleteConfirmation($id): void
    {
        $this->dialog()->confirm([
            'icon' => 'error',
            'title' => trans('Are you Sure?'),
            'description' => trans('Delete resource selected'),
            'acceptLabel' => trans('Yes, delete it'),
            'method' => 'confirmDelete',
            'params' => $id,
        ]);
    }

    public function confirmDelete(Hub $hub): void
    {
        $this->authorize('delete',$hub);
        $hub->delete();
        $this->refreshDirectories();
        $this->notification()->error(
            $title = trans('Action status'),
            $description = trans('Action run with success')
        );
    }

    #[On('refreshDirectories')]
    public function refreshDirectories(): void
    {
        $this->hubs = Hub::where('user_id',Auth::id())->get();
    }

    public function add(): void
    {
        $this->authorize('create',Hub::class);
        $this->dispatch('openModal', 'modal.tenant.backend.hub.create');
    }

    public function show($hub_id): void
    {
        $this->authorize('view',Hub::find($hub_id));
        redirect()->route('hub.show',['hub'=> $hub_id]);
    }

    public function render()
    {
        return view('pages.tenant.backend.hub.index');
    }

}
