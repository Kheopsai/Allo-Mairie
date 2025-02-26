<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user):bool
    {
        return $user->hasPermission('user-index');
    }

    public function create(User $user):bool
    {
        return $user->hasPermission('user-create');
    }

    public function update(User $user,User $target):bool
    {
        return $user->hasPermission('user-update');
    }

    public function delete(User $user,User $target):bool
    {
        return $user->hasPermission('user-delete');
    }

}
