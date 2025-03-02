<?php

namespace App\Policies;

use App\Models\Source;
use App\Models\SyncedUser;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SourcePolicy
{
    use HandlesAuthorization;

    public function viewAny(User|SyncedUser $user):bool
    {
        return $user->hasPermission('source-index');
    }

    public function view(User|SyncedUser $user,Source $chat):bool
    {
        return $user->hasPermission('source-read');
    }

    public function create(User|SyncedUser $user):bool
    {
        return $user->hasPermission('source-create');
    }


    public function update(User|SyncedUser $user,Source $chat):bool
    {
        return $user->hasPermission('source-update');
    }

    public function delete(User|SyncedUser $user,Source $chat):bool
    {
        return $user->hasPermission('source-delete');
    }
}
