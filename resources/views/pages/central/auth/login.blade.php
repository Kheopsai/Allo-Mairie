<div class="flex items-center justify-center h-full p-4 lg:p-10 xl:p-40 col-span-1">
    <div class="w-full max-w-lg space-y-4">
        <div>
            <span wire:ignore class="font-bold text-center text-gray-900 login sm:text-2xl xl:text-2xl font-pj lg:text-left">
            </span>
            <p class="text-sm font-light mt-4">
                {{ __("Please enter your email and password to access your account.") }}
            </p>
        </div>
        <form wire:submit.prevent="save">
            <div class="grid grid-cols-1 gap-6">
                <div>
                    <x-input label="{{ __('Email address') }}" placeholder="{{ __('Email address') }}" type="email" wire:model="email"/>
                </div>
                <div>
                    <x-password label="{{ __('Password') }}" placeholder="{{ __('Password') }}" wire:model="password"/>
                </div>
                <div class="flex justify-between items-center">
                    <div>
                        <x-checkbox label="{{ __('Remember me') }}" wire:model="remember"/>
                    </div>
                    <div>
                        @if (Route::has('password.request'))
                            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                               href="{{ route('password.request') }}" wire:navigate>
                                {{ __('Forgot your password?') }}
                            </a>
                        @endif
                    </div>
                </div>
                <div class="relative">
                    {{-- <x-atoms::border-beam> --}}
                        {{-- <x-slot:body class="bg-primary-500 hover:bg-primary-600 border-none"> --}}
                            <x-button sm class="relative block w-full" spinner.longest='save' type="submit" primary label="{{ __('Login') }}"/>
                        {{-- </x-slot:body> --}}
                    {{-- </x-atoms::border-beam> --}}
                </div>
                <p class="text-sm font-light">
                    {{ __("Don't have your account yet? ") }}
                    <a class="font-medium text-primary-500 transition-all duration-200 hover:underline hover:text-primary-700" href="{{ route('register') }}">{{ __('Sign up') }}</a>
                </p>
            </div>
        </form>
    </div>
</div>


