<?php

namespace App\View\Pages\Tenant\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.templates.auth')]
class Login extends Component
{

    #[Validate('required')]
    public string $email = '';

    #[Validate('required')]
    public string $password = '';

    #[Validate('boolean')]
    public bool $remember = false;

    public function save()
    {
        $this->validate();
        if(! Auth::attempt($this->only('email','password'),$this->remember))
        {
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }
        return redirect()->intended('dashboard');
    }
    public function render()
    {
        return view('pages.tenant.auth.login');
    }
}
