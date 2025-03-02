<x-organismes.section>
    <x-slot name="title">
        {{ __('My Profile') }}
    </x-slot>

    <div class="grid grid-cols-3">
        <x-card class="bg-white col-span-2" shadow="shadow">
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
                    <div class="grid grid-cols-1 gap-4">
                        <div class="grid grid-cols-2 gap-4">
                            <x-input label="{{ __('First Name') }}" wire:model.live='first_name' />
                            <x-input label="{{ __('Last Name') }}" wire:model.live='last_name' />
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <x-input label="{{ __('Email') }}" wire:model.live='email' />
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <div class="font-semibold text-sm">
                            {{ __('Profile Photo') }}
                        </div>
                        <div class="text-xs">
                            {{ __('Upload a profile photo to personalize your account.') }}
                        </div>
                    </div>
                    <div>
                        <livewire:components.atoms.dropzone :media="$media" wire:model.live="media" :rules="['image', 'mimes:png,jpeg', 'max:10420']"/>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <div class="font-semibold text-base">
                            {{ __('Change Password') }}
                        </div>
                        <div class="text-xs">
                            {{ __('Ensure your account is using a long, random password to stay secure.') }}
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-4">
                        <div class="grid grid-cols-2 gap-4">
                            <x-input label="{{ __('New Password') }}" type="password" wire:model.live="password"/>
                            <x-input label="{{ __('Confirm Password') }}" type="password" wire:model.live="password_confirmation"/>
                        </div>
                        <div class="mt-2 space-y-2 text-sm">
                            <div class="flex items-center space-x-2">
                                <input class="border-secondary-200 checked:bg-positive-500" type="radio" disabled @if($this->passwordHasLetter) checked @endif />
                                <span>{{ __('Contains at least one letter') }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <input class="border-secondary-200 checked:bg-positive-500" type="radio" disabled @if($this->passwordHasMixedCase) checked @endif />
                                <span>{{ __('Contains uppercase and lowercase letters') }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <input class="border-secondary-200 checked:bg-positive-500" type="radio" disabled @if($this->passwordHasNumber) checked @endif />
                                <span>{{ __('Contains at least one number') }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <input class="border-secondary-200 checked:bg-positive-500" type="radio" disabled @if($this->passwordHasSymbol) checked @endif />
                                <span>{{ __('Contains at least one symbol') }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <input class="border-secondary-200 checked:bg-positive-500" type="radio" disabled @if($this->passwordIsMinLength) checked @endif />
                                <span>{{ __('Is at least 8 characters long') }}</span>
                            </div>
                        </div>
                    </div>
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

