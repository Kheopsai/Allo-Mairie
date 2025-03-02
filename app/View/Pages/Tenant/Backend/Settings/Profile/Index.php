<?php

namespace App\View\Pages\Tenant\Backend\Settings\Profile;

use App\Traits\HasImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;
use Livewire\WithFileUploads;
use WireUi\Traits\WireUiActions;

class Index extends Component
{
    use HasImage;
    use WithFileUploads;
    use WireUiActions;

    public string $first_name;

    public string $last_name;

    public string $email;

    public string $password = '';

    public string $password_confirmation = '';

    public array $media = [];

    public bool $passwordHasLetter = false;

    public bool $passwordHasMixedCase = false;

    public bool $passwordHasNumber = false;

    public bool $passwordHasSymbol = false;

    public bool $passwordIsMinLength = false;

    public function mount(): void
    {
        $user = Auth::user();

        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->email = $user->email;
        $this->media = $user->bindToDropzone('profile_photo');
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,'.Auth::id()],
            'password' => ['nullable', 'confirmed', 'min:8', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
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

    public function save(): void
    {
        $this->validate($this->rules());

        $user = Auth::user();
        $user->first_name = $this->first_name;
        $user->last_name = $this->last_name;
        $user->email = $this->email;

        if (! empty($this->password)) {
            $user->password = $this->password;
        }

        $user->save();

        $user->syncImage($this->media);


        $this->reset('password', 'password_confirmation', 'passwordHasLetter', 'passwordHasMixedCase', 'passwordHasNumber', 'passwordHasSymbol', 'passwordIsMinLength');
        $this->notification()->success(
            $title = trans('Action saved'),
            $description = trans('Your action was successfully saved')
        );
    }
    public function render()
    {
        return view('pages.tenant.backend.settings.profile.index');
    }
}
