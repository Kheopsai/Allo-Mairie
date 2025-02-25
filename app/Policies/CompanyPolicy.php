<?php

namespace App\Policies;

use App\Models\Metropole;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompanyPolicy
{

    use HandlesAuthorization;

    public function viewAny(User $user):bool
    {
        return $user->hasPermission('company-index');
    }

    public function create(User $user):bool
    {
        return $user->hasPermission('company-create');
    }

    public function update(User $user,Metropole $company)
    {
        return $user->hasPermission('company-update');
    }

    public function delete(User $user,Metropole $company)
    {
        return $user->hasPermission('company-delete');
    }

}
