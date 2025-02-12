@extends('components.templates.base')
@section('body')
    <div x-data="{ open: false }" @keydown.window.escape="open = false" class="flex flex-col min-h-screen bg-white">
        <div class="flex flex-1">
            <div class="flex">
                {{-- TODO --}}
                {{-- @if(tenant())
                    <div>
                        <livewire:components.organisms.bar />
                    </div>
                @endif --}}
                <div>
                    <x-organismes.side-bar/>
                </div>
            </div>
            <div class="flex items-stretch flex-1 overflow-hidden bg-secondary-50">
                <main class="flex flex-col flex-1 overflow-y-auto h-full">
                    <x-organismes.navbar/>
                    <div class="w-full space-y-10 overflow-auto soft-scrollbar h-full dark:bg-black">
                        @yield('content')
                        @isset($slot)
                            {{ $slot }}
                        @endisset
                    </div>
                </main>
            </div>
        </div>
    </div>
    {{-- @if(tenant())
        <livewire:components.atoms.speed-dial/>
    @endif --}}
@endsection
