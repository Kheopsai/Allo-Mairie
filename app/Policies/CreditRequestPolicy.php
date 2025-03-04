<?php

namespace App\Policies;

use App\Models\CreditRequest;
use App\Models\SyncedUser;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CreditRequestPolicy
{
    use HandlesAuthorization;

    public function viewAny(User|SyncedUser $user)
    {
        return $user->hasPermission('creditRequest-index');
    }

    public function view(User|SyncedUser $user,CreditRequest $creditRequest)
    {
        return $user->hasPermission('creditRequest-read');
    }

    public function create(User|SyncedUser $user)
    {
        return $user->hasPermission('creditRequest-create');
    }

    public function update(User|SyncedUser $user,CreditRequest $creditRequest)
    {
        return $user->hasPermission('creditRequest-update');
    }

    public function delete(User|SyncedUser $user,CreditRequest $creditRequest)
    {
        return $user->hasPermission('creditRequest-delete');
    }
}
