<?php

namespace App\Policies;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PlanPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user):bool
    {
        return $user->hasPermission('plan-index');
    }

    public function create(User $user):bool
    {
        return $user->hasPermission('plan-create');
    }

    public function update(User $user,Plan $plan):bool
    {
        return $user->hasPermission('plan-update');
    }

    public function delete(User $user,Plan $plan):bool
    {
        return $user->hasPermission('plan-delete');
    }

}
