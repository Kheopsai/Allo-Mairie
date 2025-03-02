<x-card title="{{ __('New user') }}">
    <x-slot name="action">
        <x-button icon="x-mark" flat wire:click="$dispatch('closeModal')" />
    </x-slot>
    <div class="grid grid-cols-1 gap-4 px-2">
        <div>
            <x-input label="{{ __('First Name') }}" type="text" wire:model.live="first_name" />
        </div>
        <div>
            <x-input label="{{ __('Last Name') }}" type="text"  wire:model.live="last_name" />
        </div>
        <div>
            <x-input label="{{ __('Email address') }}" type="email" wire:model.live="email" />
        </div>
        <div>
                        <x-select sm label='{{ __("Role") }}' placeholder='{{ __("Choose a role for the user") }}' wire:model.live="role">
                            @foreach($this->roles as $role)
                                <x-select.option value="{{ $role->name }}">{{ __($role->name) }}</x-select.option>
                            @endforeach
                        </x-select>
        </div>
        <div>
            <x-password label="{{ __('Password') }}"  wire:model.live="password" />
        </div>
    </div>
    <x-slot name="footer">
        <x-button spinner="save()" wire:click='save()' class="w-full" primary icon="arrow-path-rounded-square"
                         label="{{ __('Add user') }}" />
    </x-slot>
</x-card>
