<?php

namespace App\View\Modal\Central\Role;

use App\Models\Role;
use Livewire\Attributes\Rule;
use Livewire\Component;
use LivewireUI\Modal\ModalComponent;
use WireUi\Traits\WireUiActions;

class Update extends ModalComponent
{
    use WireUiActions;

    public Role $role;

    #[Rule('required|string|min:3|max:150')]
    public $name;

    #[Rule('required|string|min:3|max:150')]
    public $display_name;

    #[Rule('nullable|string|max:32768')]
    public $description;

    public function mount(): void
    {
        $this->name = $this->role->name;
        $this->description = $this->role->description;
        $this->display_name = $this->role->display_name;
    }

    public function save(): void
    {
        $this->validate();
        $this->role->update([
            'name' => $this->name,
            'display_name' => $this->display_name,
            'description' => $this->description,

        ]);
        $this->closeModal();
        $this->notification()->success(
            $title = trans('Action saved'),
            $description = trans('Your action was successfully saved')
        );
        $this->dispatch('refreshDatatable');
    }

    public static function modalMaxWidth(): string
    {
        return '2xl';
    }
    public function render()
    {
        return view('modal.central.role.update');
    }
}
