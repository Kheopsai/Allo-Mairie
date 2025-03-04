@props(['label','action','parameters'])
<x-button label="{{ $label ??  __('Validate')  }}" spinner="{{ $action ?? 'validate' }}" wire:click="{{ $action ?? 'validate' }}({{ $parameters }})" primary/>
