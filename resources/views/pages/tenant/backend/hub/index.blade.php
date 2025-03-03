<x-organismes.section>
    <x-slot name="title">
        {{ __('My Databases') }}
    </x-slot>

    <div>
            @can('create',App\Models\Source::class)
        <x-card card-classes="bg-white border border-secondary-200" shadow="shadow-sm">
            <x-slot:title class="space-y-2">
                <div class="text-sm">
                    {{ __('Smart Hub Detection for Seamless Source Integration') }}
                </div>
                <div class="text-xs font-light">
                    {{ __('Automatically Identify the Optimal Hub for Every Source') }}
                </div>
            </x-slot:title>
            <x-slot:action>
                <div class="space-x-4 flex items-center">

                </div>
            </x-slot:action>

            <div class="grid grid-cols-4">
                <div class="col-span-3 text-sm">
                    <h1>{{ __('Simplify your workflow with intelligent hub detection that automatically assigns each source to the most relevant hub.') }}
                    </h1>
                    <h1>
                        {{ __('Enhance efficiency and organization without manual intervention — let the system determine where your data belongs.') }}
                    </h1>
                </div>
                <div class="col-span-1">
                    <x-button spinner=""  primary wire:click="$dispatch('openModal',{component: 'modal.tenant.backend.source.create'})"
                                     class="w-full py-3 rounded-xl" icon="document-text"
                                     label="{{ __('Add sources') }}" />
                </div>
            </div>
        </x-card>
            @endcan
    </div>
    <div class="space-y-4">
        <div>
            <div class="grid gap-6">
                <div class="flex items-center justify-between">
                    <div>
                        <x-input icon="magnifying-glass" class="pl-10 min-w-72" placeholder="{{ __('Search directory') }}"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-full space-y-4 max-w-8xl">
            <div class="grid gap-4 md:grid-cols-3">
                @can('create',App\Models\Hub::class)
                <div wire:click="add()"
                     class="flex items-center p-8 text-sm text-center border border-dashed cursor-pointer border-secondary-400 rounded-xl">
                    <div>
                        <div>
                            <span class="text-base font-medium">{{ __('+ New directory') }}</span>
                        </div>
                        <div>
                            {{ __('Create directory to organize your documents efficiently.') }}
                        </div>
                    </div> hozal@mailinator.com
                </div>
                @endcan
                @foreach ($hubs as $hub)
                    <div class=relative>
                        <div class="absolute right-0 p-6 flex justify-end z-10 items-center">
                            <div class="flex items-center">
                                <x-dropdown class="w-56">
                                    {{-- <x-dropdown.item wire:click="edit({{$hub->id}})" icon="pencil"
                                                     label="{{ __('Edit') }}"/> --}}
                                    <x-dropdown.item wire:click="deleteConfirmation({{ $hub->id }})" icon="trash"
                                                     label="{{ __('Delete') }}"/>
                                </x-dropdown>
                            </div>
                        </div>
                        <div wire:click="show({{$hub->id}})"
                             class="p-6 space-y-2 border shadow bg-white rounded-xl cursor-pointer">

                            <div wire:click="show({{ $hub->id }})" class="flex items-center space-x-2">
                                <div class="text-secondary-300" wire:click="show({{$hub->id}})">
                                    <x-icon collection="lucid" class="w-12 h-12" name="circle-stack"/>
                                </div>
                                <div>
                                    <h4 class="text-base font-medium text-secondary-600">
                                        {{ $hub->name }}
                                    </h4>
                                    <span class="text-sm text-secondary-400">
                                        {{ $hub->sources->count() }}
                                    </span>
                                </div>
                            </div>

                            <div>
                                <span class="text-sm text-secondary-400">
                                    {{ __('Created') }} {{ $hub->created_at->format('d M Y') }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-organismes.section>

