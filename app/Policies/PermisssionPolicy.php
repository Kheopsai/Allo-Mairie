<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PermisssionPolicy
{
    use HandlesAuthorization;

    public function viewAll(User $user):bool
    {
        return $user->hasPermission('permission-index');
    }

    public function assigne(User $user):bool
    {
        return $this->hasPermission('permission-create');
    }
}
