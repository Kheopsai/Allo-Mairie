<?php

namespace App\View\Pages\Central\Backend\Metropole;

use App\Enums\GovernmentInstitutionType;
use App\Models\Metropole;
use App\Support\FormComponent;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;

#[Layout('components.templates.admin')]
class Update extends FormComponent
{

    public $metropole;

    #[Validate('required|min:3')]
    public $name = '';

    #[Validate('required')]
    public $type = '';



    public $password_confirmation = '';

    public $governmentInstitutions;

    public function mount(Metropole $metropole)
    {
        $this->metropole = $metropole;
        $this->name = $metropole->name;
        $this->type = $metropole->type;
        $this->governmentInstitutions = GovernmentInstitutionType::getValues();
    }

    public function save()
    {
        $this->authorize('update',$this->metropole);
        $this->validate();
        $this->metropole->update($this->only('name', 'type'));
    }
    public function render()
    {
        return view('pages.central.backend.metropole.update');
    }
}
