@use(App\Enums\ChatType)
@use(Carbon\Carbon)
<div class="flex relative grow h-full bg-gradient-to-tl from-primary-100 via-white" x-data="{ open: true, side: false }">
    {{-- <livewire:components.molecules.chat.sources.sidebar /> --}}
    <main class="w-full flex flex-col space-y-4 relative z-0">
        <div class="!m-0 max-h-[80vh] flex flex-col h-full">
            <div x-auto-scroll class="flex-1 flex flex-col grow pl-8 py-8 overflow-y-auto soft-scrollbar h-full">
                <div class="flex-1 w-full flex flex-col pr-6 max-w-screen-md mx-auto">
                    @forelse($this->groupedChats() as $date => $messages)
                        @php
                            $carbonDate = Carbon::parse($date);
                            $label = $carbonDate->isToday()
                                ? __('Today')
                                : ($carbonDate->isYesterday()
                                    ? __('Yesterday')
                                    : $carbonDate->isoFormat('LL'));
                        @endphp
                        <div class="w-full">
                            <div class="sticky top-4 z-10 my-6">
                                <div class="flex items-center">
                                    <div class="flex-1 border-t border-secondary-200"></div>
                                    <span
                                        class="px-4 text-sm font-medium text-secondary-500 bg-secondary-50">{{ $label }}</span>
                                    <div class="flex-1 border-t border-secondary-200"></div>
                                </div>
                            </div>
                            <div class="flex flex-col space-y-6">
                                @foreach ($messages as $message)
                                    @if ($message->sender == ChatType::User)
                                        <div id="message-{{ $message->id }}" class="py-6 px-8 text-secondary-600">
                                            <div class="flex flex-col">
                                                <div class="flex space-x-4 items-center">
                                                    <div class="flex space-x-2 items-center">
                                                        <div class="flex space-x-4 items-center">
                                                            <div class="p-2 rounded-full bg-secondary-200 w-8 h-8">
                                                            </div>
                                                            <div class="text-xs font-bold">{{ __('Guest') }}</div>
                                                        </div>
                                                        {{-- <div>
                                                            <x-atoms.avatar size="small"
                                                                image="{{ auth()->user()->profile_photo_url }}" />
                                                        </div> --}}
                                                        <div class="text-xs font-bold">
                                                            {{ auth()->user()->full_name }}
                                                        </div>
                                                    </div>
                                                    <div class="text-xs text-secondary-500">
                                                        {{ $message->created_at->diffForHumans() }}
                                                    </div>
                                                </div>
                                                <div
                                                    class="text-sm min-w-64 tracking-wide leading-relaxed pl-12 font-medium text-secondary-500 -mt-2">
                                                    {!! Str::markdown($message->message) !!}
                                                </div>
                                            </div>
                                        </div>
                                    @elseif($message->sender === ChatType::Assistant)
                                        <div id="message-{{ $message->id }}"
                                            class="py-6 px-8 rounded-lg border border-secondary-200 bg-white space-y-6 shadow-sm">
                                            <div class="flex flex-col">
                                                <div class="flex space-x-4 items-center">
                                                    <div class="flex space-x-4 items-center">
                                                        <div class="p-2 rounded-full bg-primary-500 w-8 h-8">
                                                            {{-- <x-atoms.avatar size="small"
                                                                image="{{ asset('images/metropole_nice.jpg') }}" /> --}}

                                                        </div>
                                                        <div class="text-xs font-bold">
                                                            {{ config('app.name') }}
                                                        </div>
                                                    </div>
                                                    <div class="text-xs text-secondary-500">
                                                        {{ $message->created_at->diffForHumans() }}
                                                    </div>
                                                </div>
                                                <div
                                                    class="text-sm min-w-64 tracking-wide leading-relaxed pl-12 font-medium text-secondary-700 editor-content">
                                                    {!! $this->renderMixedContent($message->message) !!}
                                                </div>
                                            </div>
                                            {{-- <div class="pl-12">
                                                <livewire:components.molecules.chat.tool :message="$message" wire:key="tools-{{$message->id}}"/>
                                            </div> --}}
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div wire:loading.remove wire:target="create()"
                            class="relative h-full flex flex-col items-center justify-center space-y-4">
                            <div class="flex justify-center flex-col items-center space-y-4">
                                <img src="{{ asset('image/metropole_nice.jpg') }}"
                                    class="h-full w-full object-cover max-h-12 rounded-xl">
                                <div class="text-lg font-medium text-secondary-700">
                                    {{ __('How can I help you?') }}
                                </div>
                            </div>
                            <div class="gap-2 flex space-x-2">
                                <div class="text-xs rounded-full border border-blue-200 py-2 px-4 bg-blue-100 cursor-pointer"
                                    wire:click="buildPrompt('Résumer')">
                                    {{ __('Summarize') }}
                                </div>
                                <div class="text-xs rounded-full border border-yellow-200 py-2 px-4 bg-yellow-100 cursor-pointer"
                                    wire:click="buildPrompt('Reformuler')">
                                    {{ __('Rephrase') }}
                                </div>
                                <div class="text-xs rounded-full border border-fuchsia-200 py-2 px-4 bg-fuchsia-100 cursor-pointer"
                                    wire:click="buildPrompt('Améliorer')">
                                    {{ __('Improve') }}
                                </div>
                            </div>
                        </div>
                    @endforelse
                    <div>
                        <div x-show="$wire.isLoading"
                            class="py-6 px-8 rounded-lg border border-secondary-200 bg-white space-y-6 shadow-sm">
                            <div class="flex flex-col">
                                <div class="flex space-x-4 items-center">
                                    <div class="p-2 rounded-full bg-primary-500 w-8 h-8">
                                        {{-- <x-kheops-logo-white class="text-white" /> --}}
                                    </div>
                                    <div class="text-xs font-bold">
                                        {{ config('app.name') }}
                                    </div>
                                </div>
                                <div wire:stream="content"
                                    class="text-sm min-w-64 tracking-wide leading-relaxed pl-12 font-medium text-secondary-700">
                                    <div class="flex space-x-1 h-full w-full">
                                        <div class="bg-primary-500 h-1 w-1 rounded-full animate-bounce"
                                            style="animation-delay: 0s"></div>
                                        <div class="bg-primary-500 h-1 w-1 rounded-full animate-bounce"
                                            style="animation-delay: 0.2s"></div>
                                        <div class="bg-primary-500 h-1 w-1 rounded-full animate-bounce"
                                            style="animation-delay: 0.4s"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex items-center space-x-2 z-10 px-36">
            <div class=" flex-1 peer mb-12">
                <div class="px-6">
                    <div
                        class="mx-auto max-w-screen-lg space-y-2 p-4 rounded-xl shadow-sm bg-white border border-secondary-100">
                        <div class="p-4 border border-secondary-100 rounded-lg">
                            <textarea autofocus @disabled($editable) placeholder="{{ __('Ask me anything') }}"
                                class="peer resize-none border-none rounded-none shadow-none focus:outline-none focus:!ring-0 w-full bg-transparent max-h-10 soft-scrollbar text-sm"
                                rows="2" wire:model.live="message" wire:keydown.enter="create()"
                                @keydown.enter.prevent="if ($event.shiftKey) content += '\n'"></textarea>
                        </div>
                        <div class="flex justify-end">
                            {{-- <div class="flex items-center space-x-2">
                                <x-button secondary sm light label="{{ __('Libraries') }}" icon="queue-list"
                                    disabled />
                                <x-button secondary sm light label="{{ __('Apps') }}" icon="squares-2x2" disabled />
                            </div> --}}
                            <div>
                                <x-button wire:click="create()" sm label="{{ __('Send') }}"
                                    right-icon="paper-airplane" />
                            </div>
                        </div>
                    </div>
                </div>
                {{-- <div class="px-3 flex justify-center">
                    <textarea @disabled($editable)
                        class="peer resize-none border-none rounded-none shadow-none focus:outline-none focus:!ring-0 w-full bg-transparent max-h-10 soft-scrollbar text-sm py-2"
                        rows="2" wire:model.live="message" wire:keydown.enter="create()"
                        @keydown.enter.prevent="if ($event.shiftKey) content += '\n'"></textarea>
                </div> --}}
            </div>
            <div class="h-full p-0.5 flex items-center">
                @if ($isAble)
                    <x-atoms.button wire:click="create()" label="{{ __('Generate') }}" collection="lucide"
                        rounded="rounded-lg" spinner="create()" primary sm class="block h-full" icon="sparkles" />
                @else
                    <x-atoms.button label="{{ __('Generate') }}" collection="lucide" rounded="rounded-lg"
                        spinner="create()" secondary sm class="block h-full cursor-not-allowed" icon="sparkles" />
                @endif
            </div>
        </div>
    </main>
            <div class="min-w-72 max-w-72 bg-white max-h-[100vh] lg:flex flex-col overflow-y-auto soft-scrollbar hidden">
                <div class="px-4 space-y-4 flex flex-col h-full">
                    <div class="pt-12 flex items-center justify-center">
                        <div class="flex flex-col justify-center items-center space-y-2">
                            <div class="h-12 w-12">
                                <img src="{{asset('image/logo-kheops.svg')}}" class="h-full w-full object-cover rounded-full">
                            </div>
                            <div class="text-lg font-semibold leading-relaxed" x-show="open">
                                <div>Allo-Mairie <span class="text-sm font-light">AI</span></div>
                            </div>
                        </div>
                    </div>
                    <hr class="border border-secondary-50 bg-white">
                    @if(count($this->getSafeDocuments) > 0)
                        <div class="flex-1 flex flex-col">
                            <h3 class="text-lg font-semibold mb-4">{{ __('Reference Documents') }}</h3>
                            <div class="flex-1">
                                <div class="space-y-4 pr-2">
                                    @foreach($this->getSafeDocuments as $document)
                                        <div class="py-4 pl-4 bg-white rounded-lg shadow-sm border border-gray-200">
                                            <div class="text-sm text-gray-500 mb-2">
                                                @if(isset($document['score']))
                                                    <span class="text-xs text-gray-400">
                                                        (Relevance: {{ number_format($document['score'] * 100, 1) }}%)
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="text-secondary-700 text-xs prose max-h-[5vh] overflow-y-auto soft-scrollbar pr-4">
                                                {!! $document['content'] !!}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center text-sm py-2 border border-secondary-200 rounded">
                            {{ __('No source') }}
                        </div>
                    @endif
                </div>
            </div>
</div>
