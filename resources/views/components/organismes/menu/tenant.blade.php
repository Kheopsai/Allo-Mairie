<div class="mt-6 space-y-8">
    <div class="mt-6 space-y-4">
        @foreach (config('sidebar.tenant') as $key => $menu)
            <div>
                @if(is_string($key))
                <x-atoms.collapse title="{{ __($key) }}" :open="true">
                    @foreach ($menu as $navigation)
                        <x-atoms.link href="{{ route($navigation['route']) }}">
                            <x-dynamic-component :component="'heroicon-o-' . $navigation['icon']" class="flex-shrink-0 w-5 h-5 mr-4" />
                            <span>{{ __($navigation['label']) }}</span>
                        </x-atoms.link>
                    @endforeach
                </x-atoms.collapse>
                @else
                        <x-atoms.link href="{{ route($menu['route']) }}">
                            <x-dynamic-component :component="'heroicon-o-' . $menu['icon']" class="flex-shrink-0 w-5 h-5 mr-4" />
                            <span>{{ __($menu['label']) }}</span>
                        </x-atoms.link>
                @endif

            </div>
        @endforeach
    </div>
</div>
