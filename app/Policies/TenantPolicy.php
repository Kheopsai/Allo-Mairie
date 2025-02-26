<?php

namespace App\Policies;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TenantPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user):bool
    {
        return $user->hasPermission('tenant-index');
    }

    public function create(User $user):bool
    {
        return $user->hasPermission('tenant-create');
    }

    public function update(User $user,Tenant $tenant):bool
    {
        return $user->hasPermission('tenant-update');
    }

    public function delete(User $user,Tenant $tenant):bool
    {
        return $user->hasPermission('tenant-delete');
    }
}
