@if($style)
    <x-dynamic-component wire:ignore.self component="{{ $collection }}-{{ $style }}-{{ $name }}" {{ $attributes }} />
@else
    <x-dynamic-component wire:ignore.self component="{{ $collection }}-{{ $name }}" {{ $attributes }} />
@endif
