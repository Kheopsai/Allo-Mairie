<?php

namespace App\Policies;

use App\Models\Hub;
use App\Models\SyncedUser;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class HubPolicy
{
    use HandlesAuthorization;

    public function viewAny(User|SyncedUser $user):bool
    {
        return $user->hasPermission('hub-index');
    }

    public function view(User|SyncedUser $user,Hub $chat):bool
    {
        return $user->hasPermission('hub-read');
    }

    public function create(User|SyncedUser $user):bool
    {
        return $user->hasPermission('hub-create');
    }


    public function update(User|SyncedUser $user,Hub $chat):bool
    {
        return $user->hasPermission('hub-update');
    }

    public function delete(User|SyncedUser $user,Hub $chat):bool
    {
        return $user->hasPermission('hub-delete');
    }
}
