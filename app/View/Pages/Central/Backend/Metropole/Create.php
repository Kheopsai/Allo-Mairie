<?php

namespace App\View\Pages\Central\Backend\Metropole;

use App\Enums\GovernmentInstitutionType;
use App\Models\Metropole;
use App\Models\User;
use App\Support\FormComponent;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;

#[Layout('components.templates.admin')]
class Create extends FormComponent
{
    #[Validate('required|min:3')]
    public $name = '';

    #[Validate('required')]
    public $type='';

    #[Validate('required|min:3')]
    public $first_name = '';

    #[Validate('required|min:3')]
    public $last_name = '';

    #[Validate(['required', 'email', 'unique:users,email'])]
    public $email = '';

    #[Validate('required|min:8|confirmed')]
    public $password = '';


    public $password_confirmation = '';

    public $governmentInstitutions;

    public function mount()
    {
        $this->governmentInstitutions= GovernmentInstitutionType::getValues();
    }

    public function save()
    {
        $this->authorize('create',Metropole::class);
        $this->validate();
        $user = User::create($this->only(['email', 'password','first_name','last_name']));
        $user->metropoles()->create($this->only('name','type'));
    }

    public function render()
    {
        return view('pages.central.backend.metropole.create');
    }
}
