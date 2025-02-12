@if(count($attributes['actions']))
    <x-dropdown class="z-10 bg-white origin-top-right" width="min-w-56 w-60" align="right-0">
        @foreach($attributes['actions'] as $action)
            @if(array_key_exists('condition',$action) && !is_null($action['condition']))
                @if($action['condition'])
                    <x-dropdown.item wire:click="{{$action['action']}}({{json_encode($attributes['id'])}})" icon="{{$action['icon'][1]}}" label="{{$action['label'][1]}}"/>
                @else
                    <x-dropdown.item wire:click="{{$action['action']}}({{json_encode($attributes['id'])}})" icon="{{$action['icon'][0]}}" label="{{$action['label'][0]}}"/>
                @endif
            @else
                @if(!is_array($action['label']))
                <x-dropdown.item wire:click="{{$action['action']}}({{json_encode($attributes['id'])}})" icon="{{$action['icon']}}" label="{{$action['label']}}"/>
                @endif
            @endif
        @endforeach
    </x-dropdown>
@endif
