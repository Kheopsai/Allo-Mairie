<?php

namespace App\View\Modal\Central\User;

use App\Enums\RoleEnum;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Rule;
use Livewire\Component;
use LivewireUI\Modal\ModalComponent;
use Stancl\Tenancy\Database\Concerns\CentralConnection;
use WireUi\Traits\WireUiActions;

class Create extends ModalComponent
{

    use WireUiActions;
    use CentralConnection;

    #[Rule(['required', 'email', 'unique:users,email'])]
    public string $email = '';

    #[Rule(['required', new Password(8)])]
    public string $password = '';

    #[Rule('required|min:3')]
    public string $first_name = '';

    #[Rule('required|min:3')]
    public string $last_name = '';

    public Tenant $tenant;

    public ?array $roles;

    public function mount(Tenant $tenant): void
    {
        $this->tenant = $tenant;
        $this->roles = Role::get()->toArray();
    }

    public function saveUser(): User
    {
        $this->validate();
        $user = new User;
        $user->first_name = $this->first_name;
        $user->last_name = $this->last_name;
        $user->email = $this->email;
        $user->password = $this->password;
        $user->save();

        return $user;
    }

    /**
     * @throws \Throwable
     */
    public function save(): void
    {
        $this->validate();
        DB::beginTransaction();
        try {
            $this->tenant->run(function () {
                $user = $this->saveUser();
                $role = Role::firstOrCreate(
                    ['name' => RoleEnum::User],
                    ['display_name' => Str()->ucfirst(RoleEnum::User)]
                );
                $user->addRole($role);
                // tenant()->addSeat();
                tenant()->subscription('default')?->incrementQuantity();
                DB::commit();

                event(new Registered($user));
            });
            $this->resetExcept('companies', 'roles');
            $this->closeModal();
            $this->notification()->success(
                $title = trans('Action saved'),
                $description = trans('Your action was successfully saved')
            );
        } catch (\Exception $exception) {
            ds($exception);
            DB::rollBack();
            $this->notification()->error(
                $title = trans('Server Error'),
                $description = trans('Action error')
            );
        }
    }

    public static function modalMaxWidth(): string
    {
        return '2xl';
    }
    public function render()
    {
        return view('modal.central.user.create');
    }
}
