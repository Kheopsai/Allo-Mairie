<div class="hidden w-full max-w-xs min-w-[20rem] max-h-screen overflow-y-auto bg-white border-r md:block border-secondary-200 soft-scrollbar h-full ">
    <div class="flex flex-col items-center w-full p-6">
        <div class="flex-1 w-full space-y-1">
            <livewire:components.organismes.menu/>
        </div>
    </div>
</div>

<div x-cloak x-show="open" class="bg-white md:hidden">
    <div class="fixed inset-0 z-50 flex">
        <div x-cloak x-show="open" x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             x-description="Off-canvas menu overlay, show/hide based on off-canvas menu state."
             class="fixed inset-0 bg-opacity-75 bg-slate-600" @click="open = false" aria-hidden="true">
        </div>

        <div x-cloak x-show="open" x-transition:enter="transition ease-in-out duration-300 transform"
             x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             x-description="Off-canvas menu, show/hide based on off-canvas menu state."
             class="relative flex flex-col flex-1 w-full max-w-sm pt-5 pb-4 bg-gradient-to-t from-primary-400 to-positive-400">

            <div x-cloak x-show="open" x-transition:enter="ease-in-out duration-300"
                 x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in-out duration-300" x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0" class="absolute right-0 p-1 top-1 -mr-14"
                 x-description="Close button, show/hide based on off-canvas menu state.">
                <button type="button"
                        class="flex items-center justify-center w-12 h-12 rounded-full focus:outline-none focus:ring-2 focus:ring-white"
                        @click="open = false">
                    <x-heroicon-o-x-mark class="w-6 h-6 text-white"/>
                </button>
            </div>

            <div class="flex flex-col items-center w-full py-6">
                {{-- <div class="flex items-center flex-shrink-0 mt-2">
                    <div class="relative">
                        <x-atoms::logo class="w-32 text-white fill-current"/>
                    </div>
                </div> --}}
                <div class="flex-1 w-full pl-12 mt-8 space-y-1">
                    <livewire:components.organismes.menu/>

                </div>
            </div>
        </div>

        <div class="flex-shrink-0 w-14" aria-hidden="true">
        </div>
    </div>
</div>
