<header class="w-full bg-white border-b border-secondary-200 dark:bg-black dark:border-secondary-950">
    {{-- <div {{ $attributes->merge(['class' => "relative z-10 flex flex-shrink-0 py-4"]) }} class="relative z-10 flex flex-shrink-0 py-4"> --}}
    <div class="relative z-10 flex flex-shrink-0 py-4">
        <x-button flat icon="bars-3-bottom-left" lg @click="open = true" class="block md:hidden focus:outline-none"/>
        <div class="flex justify-between flex-1 px-8">
            <div class="flex items-center flex-1 px-2">
                {{-- <x-molecules::breadcrumb /> --}}
            </div>
            <div class="flex items-center ml-2 space-x-4 sm:ml-6 sm:space-x-6">
                @auth
                    @if (tenant())
                        <div class="text-sm p-2 flex space-x-2 items-center dark:text-secondary-300" onclick="Livewire.dispatch('openModal',{component: 'modal.tenant.backend.product.billing'})">
                            <div>
                                <x-lucide-hand-coins class="h-4 text-yellow-400"/>
                            </div>
                            <div wire:poll >
                                {{ auth()->user()->credit }}
                            </div>
                        </div>
                    @endif
                    <div x-data="{ open: false }" class="relative flex-shrink-0">
                        <div class="flex items-center">
                            <img @click="open = true" class="w-10 h-10 rounded-full cursor-pointer object-cover shadow-sm border border-primary-500" src="{{ auth()->user()->profile_photo_url }}" alt="">
                        </div>
                        <div x-show="open"  class="absolute right-0 w-48 divide-y mt-2 origin-top-right bg-white rounded-md shadow ring-1 ring-black ring-opacity-5 focus:outline-none"
                             @click.away="open = false">
                            <div class="px-4 py-3">
                                <div class="font-medium text-sm">
                                    {{ auth()->user()->full_name }}
                                </div>
                                <div class="text-xs text-slate-700">
                                    {{ auth()->user()->email }}
                                </div>
                            </div>
                            <div>
                                @if (!tenant())
                                    <x-button href="{{ route('setting.index') }}" icon="cog-6-tooth" label="{{ __('Settings') }}" flat class="w-full flex !justify-start"/>
                                @endif
                                <form method="post" action="{{ route('logout') }}">
                                    @csrf
                                    <x-button type="submit" icon="arrow-left-start-on-rectangle" label="{{ __('Log Out') }}" flat class="w-full flex !justify-start"/>
                                </form>
                            </div>
                        </div>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</header>
