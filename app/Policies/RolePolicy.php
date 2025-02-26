<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user):bool
    {
        return $user->hasPermission('role-index');
    }

    public function create(User $user):bool
    {
        return $user->hasPermission('role-create');
    }

    public function update(User $user,Role $role):bool
    {
        return $user->hasPermission('role-update');
    }

    public function delete(User $user,Role $role):bool
    {
        return $user->hasPermission('role-delete');
    }
}
