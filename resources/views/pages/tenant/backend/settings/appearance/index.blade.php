<x-organismes.section>
    <x-slot name="title">
        {{ __('Appearance') }}
    </x-slot>

    <div class="grid grid-cols-3">
        <x-card class="bg-white col-span-2" shadow="shadow">
            <div class="grid grid-cols-1 gap-8 p-4">
                <div class="col-span-1 space-y-2">
                    <div class="font-semibold text-sm">
                        {{ __('Platforme logo') }}
                    </div>
                    <div class="text-xs">
                        {{ __('Select image or logo of platforme.') }}
                    </div>
                    <div>
                        <livewire:components.atoms.dropzone :media="$media" wire:model.live="media" :rules="['image', 'mimes:png,jpeg', 'max:10420']" />
                    </div>
                </div>
                <div>
                    <x-color-picker wire:model="color" class="w-full block" label="{{ __('Select a color') }}"
                        placeholder="{{ __('Select your brand color') }}" :colors="[
                            ['name' => 'Slate', 'value' => '#64748b'],
                            ['name' => 'Red', 'value' => '#ef4444'],
                            ['name' => 'Orange', 'value' => '#f97316'],
                            ['name' => 'Amber', 'value' => '#f59e0b'],
                            ['name' => 'Yellow', 'value' => '#facc15'],
                            ['name' => 'Lime', 'value' => '#84cc16'],
                            ['name' => 'Green', 'value' => '#22c55e'],
                            ['name' => 'Emerald', 'value' => '#10b981'],
                            ['name' => 'Teal', 'value' => '#14b8a6'],
                            ['name' => 'Cyan', 'value' => '#06b6d4'],
                            ['name' => 'Sky', 'value' => '#38bdf8'],
                            ['name' => 'Blue', 'value' => '#3b82f6'],
                            ['name' => 'Indigo', 'value' => '#6366f1'],
                            ['name' => 'Violet', 'value' => '#8b5cf6'],
                            ['name' => 'Purple', 'value' => '#a855f7'],
                            ['name' => 'Fuchsia', 'value' => '#d946ef'],
                            ['name' => 'Pink', 'value' => '#ec4899'],
                            ['name' => 'Rose', 'value' => '#f43f5e'],
                        ]" />
                </div>
            </div>
            <x-slot name="footer">
                <div class="flex items-center justify-end">
                    <x-button spinner label="{{ __('Save') }}" primary wire:click="save" />
                </div>
            </x-slot>
        </x-card>
    </div>
</x-organismes.section>

