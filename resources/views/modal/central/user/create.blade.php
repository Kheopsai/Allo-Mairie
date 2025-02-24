<x-card title="{{ __('New user') }}">
    <x-slot name="action">
        <x-button icon="x-mark" flat wire:click="$dispatch('closeModal')"/>
    </x-slot>
    <div class="grid grid-cols-1 gap-4 px-2">
        <div>
            <x-input sm lg placeholder="{{ __('First Name') }}" type="text"
                            wire:model.live="first_name"/>
        </div>
        <div>
            <x-input sm lg placeholder="{{ __('Last Name') }}" type="text" wire:model.live="last_name"/>
        </div>
        <div>
            <x-input sm lg placeholder="{{ __('Email address') }}" type="email" wire:model.live="email"/>
        </div>
        <div>
            <x-password sm lg placeholder="{{ __('Password') }}" wire:model.live="password"/>
        </div>
    </div>
    <x-slot name="footer">
        <x-button spinner wire:click="save" class="w-full" primary icon="tag"
                         label="{{ __('Add user') }}"/>
    </x-slot>
</x-card>
