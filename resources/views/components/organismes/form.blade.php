<div class="grid gap-12 py-4">
    <div class="flex justify-between items-center">
        <div>
            @isset($title)
                <div class="text-lg font-semibold">
                    {{ __($title) }}
                </div>
            @endisset
            @isset($description)

            <div class="text-sm">
                {{ __($description) }}
            </div>
            @endisset
        </div>
        <div class="justify-end block space-x-2">
            <x-button class="block" white wire:click="cancel" label="{{ __('Cancel') }}" />
            <x-button spinner="save" wire:click="store" class="block" primary label="{{ __('Save') }}" />
        </div>
    </div>
    <div>
        <hr>
    </div>
    <div>
        {{ $slot }}
    </div>
</div>
