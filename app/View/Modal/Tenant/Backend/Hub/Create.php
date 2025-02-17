<?php

namespace App\View\Modal\Tenant\Backend\Hub;

use App\Models\Hub;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Rule;
use LivewireUI\Modal\ModalComponent;
use WireUi\Traits\WireUiActions;

class Create extends ModalComponent
{
    use WireUiActions;

    #[Rule(['required', 'min:3'])]
    public $name;

    public function forceCloseModal()
    {
        $this->forceClose()->closeModal();
    }

    public static function modalMaxWidth(): string
    {
        return '2xl';
    }

    public function save(): void
    {
        $this->validate();
        Hub::create([
            'name' => $this->name,
            'user_id' => Auth::id(),
        ]);
        $this->forceCloseModal();
        $this->dispatch('refreshDirectories');
        $this->notification()->success(
            $title = trans('Action saved'),
            $description = trans('Your action was successfully saved')
        );
    }
    public function render()
    {
        return view('modal.tenant.backend.hub.create');
    }
}
