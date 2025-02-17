<x-card title="{{ __('New Category') }}">
    <x-slot name="action">
        <x-button icon="x-mark" flat wire:click="forceCloseModal()" />
    </x-slot>
    <div class="grid grid-cols-1 gap-4 px-2">
        <div>
            <x-input wire:model='name' label="{{ __('Resrouce name') }}" />
        </div>
    </div>
    <x-slot name="footer">
        <x-button spinner wire:click="save()" class="w-full" primary icon="folder-open"
            label="{{ __('Add directory') }}" />
    </x-slot>
</x-card>
