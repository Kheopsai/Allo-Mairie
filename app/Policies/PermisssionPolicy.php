<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PermisssionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user):bool
    {
        return $user->hasPermission('permission-index');
    }

    public function assign(User $user):bool
    {
        return $user->hasPermission('permission-create');
    }
}
