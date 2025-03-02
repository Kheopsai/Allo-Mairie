<?php

namespace App\View\Pages\Tenant\Backend\Settings;

use App\Enums\RoleEnum;
use App\Models\Metropole;
use App\Models\Permission;
use App\Models\Role;
use App\Models\SyncedUser;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.templates.admin')]
class Index extends Component
{
    public $settings = [];

    public function mount()
    {
        $this->settings=[
            'Profile',
            Auth::user()->HasRole(RoleEnum::Admin) ? 'Organization':null,
            Auth::user()->HasRole(RoleEnum::Admin) ? 'Appearance':null,
            Auth::user()->can('viewAny',User::class) ? 'Users':null,
            Auth::user()->can('viewAny',Role::class) ? 'Roles':null,
            Auth::user()->can('viewAny',Permission::class) ? 'Permissions':null,

        ];
        // if (Auth::user()->hasRole(RoleEnum::Admin)) {
        //     $this->settings = ['Profile', 'Organization', 'Appearance', 'Users', 'Roles','Permissions'];
        // } else {
        //     $this->settings = ['Profile'];

        // }
        $this->settings= array_filter($this->settings);
    }
    public function render()
    {
        return view('pages.tenant.backend.settings.index');
    }
}
