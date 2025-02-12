@extends('components.templates.base')

@section('body')
    <div class="relative grid grid-cols-2">
        <a href="{{ route('welcome') }}" class="flex space-x-2 lg:absolute p-4 col-span-2 items-center">
            {{-- <x-atoms::logo height="2em" width="2em"/> --}}
            <div class="flex space-x-1 items-end">
                <div class="text-2xl font-semibold text-secondary-900">
                    {{ config('app.name') }}
                </div>
                <div class="text-xs font-light">
                    AI
                </div>
            </div>
        </a>
        @isset($slot)
            {{ $slot }}
        @endisset
        <div class="relative flex h-screen overflow-hidden place-items-center col-span-1 p-4">
            <div class="h-full w-full bg-secondary-100 rounded-xl flex justify-center items-center relative p-8 flex-col">

                <div class="absolute top-0 right-0 -mb-10">
                    {{-- <x-kheops-dotted class="w-96 h-96 opacity-10"/> --}}
                </div>
                <div class="m-0 grow items-center flex">
                    <div class="space-y-4 max-w-screen-sm">
                        {{-- <x-atoms::logo class="animate-pulse"/> --}}
                        <div>
                            <h1 class="text-lg font-semibold">{{ __('messages.welcome_message.title') }}</h1>
                            <p class="animate-pulse">{{ __('messages.welcome_message.line1') }}</p>
                            <p class="animate-pulse">{{ __('messages.welcome_message.line2') }}</p>
                            <p class="animate-pulse">{{ __('messages.welcome_message.line3') }}</p>
                            <p class="animate-pulse">{{ __('messages.welcome_message.footer') }}</p>
                        </div>
                        <div class="flex space-x-2">
                            <div>
                                <x-lucide-audio-lines class="h-4 text-secondary-400"/>
                            </div>
                            <div>
                                <x-lucide-refresh-cw class="h-4 text-secondary-400"/>
                            </div>
                            <div>
                                <x-lucide-copy class="h-4 text-secondary-400"/>
                            </div>
                            <div>
                                <x-lucide-sparkles class="h-4 text-secondary-400"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-full">
                    <div class="bg-white rounded-full border border-secondary-200 shadow-sm flex-1 peer">
                        <div class="h-12 items-center justify-between flex px-4">
                            <div class="flex items-center space-x-4">
                                <div>
                                    <x-lucide-search class="h-6 text-secondary-300"/>
                                </div>
                                <div class="auth-chat text-secondary-700 text-sm">

                                </div>
                            </div>
                            <div>
                                <x-lucide-arrow-up class="h-6 text-secondary-300"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
