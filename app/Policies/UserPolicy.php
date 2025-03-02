<?php

namespace App\Policies;

use App\Models\SyncedUser;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User|SyncedUser $user):bool
    {
        return $user->hasPermission('user-index');
    }

    public function create(User|SyncedUser $user):bool
    {
        return $user->hasPermission('user-create');
    }

    public function update(User|SyncedUser $user,User|SyncedUser $target):bool
    {
        return $user->hasPermission('user-update');
    }

    public function delete(User|SyncedUser $user,User|SyncedUser $target):bool
    {
        return $user->hasPermission('user-delete');
    }

}
