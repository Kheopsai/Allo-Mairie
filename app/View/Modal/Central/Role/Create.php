<?php

namespace App\View\Modal\Central\Role;

use App\Models\Role;
use Livewire\Attributes\Rule;
use LivewireUI\Modal\ModalComponent;
use WireUi\Traits\WireUiActions;

class Create extends ModalComponent
{
    use WireUiActions;
    #[Rule('required|string|min:3|max:150')]
    public $name;

    #[Rule('required|string|min:3|max:150')]
    public $display_name;

    #[Rule('nullable|string|max:32768')]
    public $description;

    public function save(): void
    {
        $this->authorize('create',Role::class);
        $this->validate();
        Role::create([
            'name' => $this->name,
            'display_name' => $this->display_name,
            'description' => $this->description,

        ]);
        $this->reset();
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
        return view('modal.central.role.create');
    }
}
