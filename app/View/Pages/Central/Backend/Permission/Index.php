<?php

namespace App\View\Pages\Central\Backend\Permission;

use App\Enums\RoleEnum;
use App\Helpers\CollectionPaginator;
use App\Models\Permission;
use App\Models\Role;
use App\Support\FormComponent;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

#[Layout('components.templates.admin')]
class Index extends FormComponent
{
    use WithPagination;
    use WireUiActions;

    public $roles;
    public $selectedPermissions = [];
    public $assignAllPermissions = [];

    public function mount()
    {
        // Retrieve all roles
        $this->roles = Role::whereNot('name', RoleEnum::Admin)->get();
        $this->getPermissionByRole();
    }

    public function getPermissionByRole()
    {
        foreach ($this->roles as $key => $role) {
            foreach ($role->permissions->pluck('name')->toArray() as $key => $permission) {
                $parts = explode('-', $permission);
                $this->selectedPermissions[$role->id][$permission] = true;
            }
        }
    }

    public function updatedAssignAllPermissions($id,$value)
    {
        foreach ($this->assignAllPermissions as $key => $role) {

            $permissions = $this->getPermissionsProperty()->only(array_keys($role));
            foreach ($permissions as $permission) {
                $this->selectedPermissions[$key][$permission] = true;
            }
        }
    }

    public function getPermissionsProperty()
    {
        $permissions = Permission::all();

        $models = $permissions->map(function ($permission) {
            return Str::before($permission->name, '-');
        })->unique()->values();
        // Loop through each model
        $model_permissions = collect(); // Initialize an empty collection

        // Loop through each model
        foreach ($models as $model) {
            // Retrieve the permissions associated with the current model
            $permissionsForModel = $permissions->filter(function ($permission) use ($model) {
                return Str::startsWith($permission->name, strtolower($model) . '-');
            })->pluck('name')->toArray();

            // Add the permissions to the collection using the model name as the key
            $model_permissions->put($model, $permissionsForModel);
        }
        $pagination = new CollectionPaginator();
        return $pagination->paginate($model_permissions, 6);
    }

    public function save()
    {
        $this->authorize('assign', Permission::class);
        foreach ($this->selectedPermissions as $role => $permissions) {
            $role = Role::find($role);
            $synced = [];
            foreach ($this->hyphen($permissions) as $permission => $check) {
                if ($check) {
                    $synced[] = $permission;
                }
            }
            $role->syncPermissions($synced);
        }
    }

    public function setRedirectAfterActionRoute(?string $route = null): string
    {
        return 'permission.index';
    }


    public function hyphen($arr)
    {
        $flattened = Arr::dot($arr);
        $result = [];

        foreach ($flattened as $key => $value) {
            $result[str_replace('.', '-', $key)] = $value;
        }
        return $result;
    }

    public function render()
    {
        return view('pages.central.backend.permission.index');
    }
}
