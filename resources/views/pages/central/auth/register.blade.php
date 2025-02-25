<div class="flex items-center justify-center px-4 py-10 bg-white sm:px-6 lg:px-8 sm:py-16 lg:py-24"
     x-data="{ step: @entangle('step').live  }">
    <div class="w-full max-w-lg space-y-4">
        <h2 class="text-3xl font-bold leading-tight text-black sm:text-4xl">
            {{ __('Sign up') }}
        </h2>
        <p class="text-sm font-light">
            {{ __('Already have an account? ') }}
            <a class="font-medium text-primary-500 transition-all duration-200 hover:underline hover:text-primary-700" href="{{ route('login') }}">{{ __('Login') }}</a>
        </p>

        <div class="flex items-center space-x-2 mt-6">
            <div :class="step >= 1 ? 'bg-primary-500' : 'bg-secondary-200'" class="h-1 w-10 rounded flex-1"></div>
            <div :class="step >= 2 ? 'bg-primary-500' : 'bg-secondary-200'" class="h-1 w-10 rounded flex-1"></div>
            <div :class="step === 3 ? 'bg-primary-500' : 'bg-secondary-200'" class="h-1 w-10 rounded flex-1"></div>
        </div>

        <form wire:submit.prevent="save" class="space-y-6">
            <template x-if="step === 1">
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-2">
                        <x-input class="w-full flex-1" label="{{ __('First name') }}" placeholder="{{ __('First name') }}" type="text" wire:model="first_name"/>

                        <x-input class="w-full" label="{{ __('Last name') }}" placeholder="{{ __('Last name') }}" type="text" wire:model="last_name"/>
                    </div>
                    <x-input label="{{ __('Email address') }}" placeholder="{{ __('Email address') }}" type="email" wire:model="email"/>
                </div>
            </template>
            <template x-if="step === 2">
                <div class="space-y-4">
                    <div>
                        <x-input label="{{ __('Company Name') }}" placeholder="{{ __('Company Name') }}" type="text"  wire:model="enterprise_name"/>
                    </div>
                    <div>
                        <x-select sm label='{{ __("Institution of company") }}' placeholder='{{ __("Institution of company") }}' wire:model.live="institution">
                            @foreach($institutions as $institution)
                                <x-select.option value="{{ $institution }}">{{ __($institution) }}</x-select.option>
                            @endforeach
                        </x-select>
                    </div>
                    <div class="grid grid-cols-5 gap-2">
                        <div class="col-span-5">
                            <x-label label="{{__('Phone Number')}}"/>
                        </div>
                        {{-- <div>
                            <x-select :searchable="false" wire:model.live="country">
                                @foreach($countries as $country)
                                    <x-select.option value="{{ $country->phonecode }}">
                                        <div class="flex justify-between space-x-2 items-center">
                                            <div>
                                                @svg("flag-country-".Str::lower($country->iso),'h-4')
                                            </div>
                                            <div>
                                                {{ $country->phonecode }}
                                            </div>
                                        </div>
                                    </x-select.option>
                                @endforeach
                            </x-select>
                        </div> --}}
                        {{-- <div class="col-span-4">
                            <x-inputs.phone mask="['## ## ## ## ##', '# ## ## ## ##', '(0) # ## ## ## ##']" placeholder="{{ __('Phone Number') }}" type="text" wire:model="phone" />
                        </div> --}}
                    </div>
                </div>
            </template>
            <template x-if="step === 3">
                <div class="space-y-4">
                    <div>
                        <x-password label="{{ __('Password') }}" placeholder="{{ __('Password') }}" wire:model.live="password"/>

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
                    <div>
                        <x-password label="{{ __('Confirm Password') }}" type="password" wire:model.live="password_confirmation"/>
                    </div>
                    {{-- <div class="flex items-center space-x-2">
                        <div>
                            <x-checkbox lg wire:model='agree'/>
                        </div>
                        <div>
                            {{ __('I agree with') }} <a href="{{ route('pages',['page' => 'terms']) }}" class="font-semibold">{{ __('Terms & Conditions') }}</a>
                        </div>
                    </div> --}}
                </div>
            </template>

            <div class="flex" :class="step === 1 ? 'justify-end' : 'justify-between'">
                <div class="flex justify-end pt-4" x-show="step >= 2">
                    <x-button class="px-4 py-2" wire:click="back()" spinner="back()" label="{{ __('Back') }}"/>
                </div>
                <div class="flex justify-end pt-4" x-show="step <= 2">
                    <x-button black class="px-4 py-2" wire:click="next()" spinner="next()" label="{{ __('Next') }}"/>
                </div>
                <div class="flex justify-end pt-4" x-show="step === 3">
                    <x-button primary class="px-4 py-2" wire:click="save()" spinner="save()" label="{{ __('Submit') }}"/>
                </div>
            </div>
        </form>
    </div>
</div>

