<x-organismes.section>
    <x-slot name="title">
        {{ __('Companie information') }}
    </x-slot>


    <div class="grid grid-cols-3">
        <x-card cardClasses="bg-white col-span-2" shadow="shadow">
            <div class="grid grid-cols-1 gap-8 p-4">
                <div class="space-y-4">
                    <div>
                        <div class="font-semibold text-base">
                            {{ __('General information') }}
                        </div>
                        <div class="text-xs">
                            {{ __('Update your account\'s profile information and email address.') }}
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-8">
                        <div>
                            <x-input wire:model.blur="name" label="{{ __('Name of company') }}"/>
                        </div>
                    </div>
                </div>
                <div class="space-y-4">
                    <div>
                        <div class="font-semibold text-sm">
                            {{ __('Company picture') }}
                        </div>
                        <div class="text-xs">
                            {{ __('Upload a profile photo to personalize your account.') }}
                        </div>
                    </div>
                    <div>
                        <livewire:components.atoms.dropzone :media="$media" wire:model.live="media" :rules="['image', 'mimes:png,jpeg', 'max:10420']"/>
                    </div>
                </div>
            </div>
            <x-slot name="footer">
                <div class="flex items-center justify-end">
                    <x-button spinner label="{{ __('Save') }}" primary wire:click="save"/>
                </div>
            </x-slot>
        </x-card>
    </div>
</x-organismes.section>

