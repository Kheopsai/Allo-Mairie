<div x-data="{ value: 'Profile' }" class="flex h-full">
    <div class="h-full">
        <div class="relative bg-white border-r border-secondary-200 shadow-sm transition-all flex flex-col duration-300 z-10 h-full w-72">
            <div class="overflow-y-auto overflow-x-hidden h-full soft-scrollbar flex-grow">
                @foreach ($settings as $setting)
                    <div  @click="value='{{ $setting }}'" :class="{'border-primary-500 text-primary-500': value === '{{ $setting }}'}" class="chat-ai relative cursor-pointer group/item h-[48px] w-full flex items-center gap-3 pl-5 pr-4 hover:bg-secondary-50 transition duration-200 ease-out border-0 border-r-2 hover:border-primary-500">
                        <h4 class=" text-secondary-700 whitespace-nowrap text-sm">{{ trans($setting) }}</h4>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="w-full">
        <div x-cloak x-show="value ==='Profile'" wire:key='Profile'>
            <livewire:pages.tenant.backend.settings.profile.index wire:key='Profile' />
        </div>
         <div x-cloak x-show="value ==='Organization'" wire:key='Organization'>
            <livewire:pages.tenant.backend.settings.organization.index wire:key='Organization' />
        </div>
        <div x-cloak x-show="value ==='Appearance'" wire:key='Appearance'>
            <livewire:pages.tenant.backend.settings.appearance.index wire:key='Appearance' />
        </div>

        <div x-cloak x-show="value === 'Permissions'" wire:key='Permissions'>
            <livewire:pages.tenant.backend.settings.permission.index wire:key='Permissions' />
        </div>
        <div x-cloak x-show="value ==='Roles'" wire:key='Roles'>
            <livewire:pages.tenant.backend.settings.role.index wire:key='Roles' />
        </div>
        <div x-cloak x-show="value ==='Users'" wire:key='Users'>
            <livewire:pages.tenant.backend.settings.user.index wire:key='Users' />
        </div>
    </div>
</div>
