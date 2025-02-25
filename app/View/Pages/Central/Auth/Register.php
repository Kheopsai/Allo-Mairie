<?php

namespace App\View\Pages\Central\Auth;

use App\Enums\GovernmentInstitutionType;
use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.templates.auth')]
class Register extends Component
{

    public int $step = 1;

    public string $type = '';

    public string $first_name = '';

    public string $last_name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public bool $agree = false;

    public string $enterprise_name = '';

    public array $institutions;

    public string $institution = '';

    public bool $passwordHasLetter = false;

    public bool $passwordHasMixedCase = false;

    public bool $passwordHasNumber = false;

    public bool $passwordHasSymbol = false;

    public bool $passwordIsMinLength = false;

    public function mount(): void
    {
        $this->institutions = GovernmentInstitutionType::getValues();
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'institution' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
            'enterprise_name' => ['required', 'min:3'],
        ];
    }

    public function updatedPassword($value): void
    {
        $this->passwordHasLetter = (bool) preg_match('/[a-zA-Z]/', $value);

        $this->passwordHasMixedCase = (bool) (preg_match('/[a-z]/', $value) && preg_match('/[A-Z]/', $value));

        $this->passwordHasNumber = (bool) preg_match('/\d/', $value);

        $this->passwordHasSymbol = (bool) preg_match('/[!@#$%^&*(),.?":{}|<>]/', $value);

        $this->passwordIsMinLength = strlen($value) >= 8;
    }

    public function personalValidate(): array
    {
        return [
            'first_name' => 'required|min:3',
            'last_name' => 'required|min:3',
            'email' => 'required|email|unique:users',
        ];
    }

    public function companyValidate(): array
    {
        $rules = [
            'enterprise_name' => 'sometimes|min:3',
        ];

        return $rules;
    }

    public function next(): void
    {
        switch ($this->step) {
            case 1:
                $this->validate($this->personalValidate());
                break;
            case 2:
                $this->validate($this->companyValidate());
                break;
        }
        $this->step++;
    }

    public function back(): void
    {
        $this->step--;
    }

    /**
     * @throws Throwable
     */
    public function save()
    {

        $this->validate();
        $user = User::create([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'password' => $this->password,
        ]);
        $company = $user->metropoles()->create([
            'name' => $this->enterprise_name,
            'type' => $this->institution,
        ]);
        if (is_null($user->metropole_id)) {
            $user->metropole_id = $company->id;
            $user->save();
        }
        $user->addRole(RoleEnum::Busniss);
        event(new Registered($user));

        // Auth::login($user, true);

        return redirect()->route('login');
    }
    public function render()
    {
        return view('pages.central.auth.register');
    }
}
