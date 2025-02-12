<div>
    @isset($actions)
        <x-dropdown class="bg-white z-10 w-56">
            <x-slot name="trigger">
                <x-button spinner="add()" white class="text-sm !text-gray-700 border border-gray-300 rounded-md">
                    <div class="flex space-x-4 items-center">
                        <div>
                            {{ $label ?? __('Add') }}
                        </div>
                        <div>
                            <x-atoms.icon collection="lucide" name="chevron-down" class="w-4 h-4 text-secondary-500 hover:text-secondary-700 dark:hover:text-secondary-600 transition duration-150 ease-in-out"/>
                        </div>
                    </div>
                </x-button>
            </x-slot>
            @foreach($actions as $action)
                <x-dropdown.item wire:click="{{$action['action']}}({{json_encode($attributes['id'])}})" icon="{{$action['icon']}}" label="{{$action['label']}}"/>
            @endforeach
        </x-dropdown>
    @else
        <x-button spinner="add()" wire:click='add()' white class="text-sm !text-gray-700 border border-gray-300 rounded-md" label="{{ $label ?? __('Add') }}"/>
    @endisset
</div>
