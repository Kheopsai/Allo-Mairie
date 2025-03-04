<?php

namespace App\View\Modal\Tenant\Backend\User;

use App\Enums\RoleEnum;
use App\Models\Role;
use App\Models\SyncedUser;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Rule;
use Livewire\Component;
use LivewireUI\Modal\ModalComponent;
use WireUi\Traits\WireUiActions;

class Create extends ModalComponent
{
    use WireUiActions;

    #[Rule('required|min:3')]
    public $first_name;

    #[Rule('required|min:3')]
    public $last_name;

    #[Rule('required|email|unique:users')]
    public $email;

    #[Rule('required|min:8')]
    public $password;

    #[Rule('required')]
    public $role= RoleEnum::User;

    #[Computed]
    public function roles()
    {
        return Role::whereNot('name',RoleEnum::Admin)->get();
    }
    /**
     * @throws Throwable
     */
    public function save(): void
    {
        $this->validate();

        try {
            DB::transaction(function () {
                $tenant = tenant();
                $company = $tenant->metropole()->first();
                if (! $company) {
                    throw new Exception('Tenant company not found.');
                }

                tenancy()->central(function () use ($company, $tenant) {
                    $user = User::create([
                        'first_name' => $this->first_name,
                        'last_name' => $this->last_name,
                        'email' => $this->email,
                        'password' => $this->password,
                    ]);

                    $user->metropoles()->attach($company);


                    $attributes = $user->getAttributes();

                    tenancy()->initialize($tenant);
                    $user =SyncedUser::create($attributes);
                    $user->addRole($this->role);
                });
            });

            $this->closeModal();
            $this->notification()->success(
                $title = trans('Action saved'),
                $description = trans('Your action was successfully saved')
            );
            $this->dispatch('refreshDatatable');
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Error saving user: ' . $exception->getMessage());

            $this->notification()->error(
                $title = trans('Action failed'),
                $description = trans('An error occurred while saving your action.')
            );
        }
    }

    public static function modalMaxWidth(): string
    {
        return '2xl';
    }
    public function render()
    {
        return view('modal.tenant.backend.user.create');
    }
}
