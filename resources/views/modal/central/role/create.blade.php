<x-card title="{{ __('New role') }}">
    <x-slot name="action">
        <x-button icon="x-mark" flat wire:click="$dispatch('closeModal')" />
    </x-slot>
    <div class="grid grid-cols-1 gap-4 px-2">
        <div>
            <x-input wire:model='name' label="{{ __('Name') }}" />
        </div>
        <div>
            <x-input wire:model='display_name' label="{{ __('Display name') }}" />
        </div>
        <div>
            <x-textarea wire:model='description' label="{{ __('Description') }}"></x-textarea>
        </div>

    </div>

    <x-slot name="footer">
        <x-button spinner wire:click="save" class="w-full" primary icon="tag"
            label="{{ __('Add role') }}" />
    </x-slot>
</x-card>
