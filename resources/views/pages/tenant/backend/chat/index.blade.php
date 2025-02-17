@use(App\Enums\ChatType)
@use(Carbon\Carbon)
<div class="flex relative overflow-hidden grow h-full" x-data="{ open: true, side: false }">
    <livewire:components.molecules.chat.sources.sidebar />
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
                                                    {!! Str::markdown($message->message) !!}
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
            <div class="bg-white rounded-full border border-secondary-200 shadow-sm flex-1 peer">
                <div class="px-3 flex justify-center">
                    <textarea @disabled($editable)
                        class="peer resize-none border-none rounded-none shadow-none focus:outline-none focus:!ring-0 w-full bg-transparent max-h-10 soft-scrollbar text-sm py-2"
                        rows="2" wire:model.live="message" wire:keydown.enter="create()"
                        @keydown.enter.prevent="if ($event.shiftKey) content += '\n'"></textarea>
                </div>
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
</div>
