<aside x-data :class="{'w-20': !open, 'w-96': open}" class="relative bg-white border-r border-secondary-200 shadow-sm transition-all flex flex-col duration-300 z-10 h-full">
    <div class="flex p-4 border-0 border-b border-secondary-200" :class="{'flex-col justify-center space-y-2': !open, 'justify-between' : open}">
        <div class="flex justify-center">
            <div :class="{'hidden': !open, 'block': open}">
                <x-button xs flat squared collection="lucide" icon="plus" wire:click="newChannel()" label="{{ __('New channel') }}"/>
            </div>
            <div :class="{'block': !open, 'hidden': open}">
                <x-button xs flat squared collection="lucide" icon="plus" wire:click="newChannel()"/>
            </div>
        </div>
        <div :class="{'justify-center w-full': !open, 'justify-end': open}" class="flex">
            <x-button xs flat collection="lucide" icon="arrow-left" @click="open = !open" x-show="open"/>
            <x-button xs flat collection="lucide" icon="arrow-right" @click="open = !open" x-show="!open"/>
        </div>
    </div>
    <div>
        <ul class="space-y-2">
            <div class="overflow-y-auto space-y-4 max-h-[85vh] soft-scrollbar p-4">
                @forelse ($channels as $channel)
                    <div class="flex items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
                        <div class="flex-1 overflow-hidden">
                            <h3 class="text-sm font-medium text-gray-800 truncate">{{ $channel['name'] }}</h3>
                            <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($channel['created_at'])->diffForHumans() }}</p>
                        </div>
                        <div class="flex space-x-2">
                            <x-button flat icon="arrow-right" primary xs wire:click="loadChannel('{{ $channel['id'] }}')"/>
                            <x-button flat icon="trash" negative xs wire:click="confirmDeleteChannel('{{ $channel['id'] }}')"/>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center h-[60vh] space-y-2">

                        <div class="text-md font-medium text-secondary-700">
                            {{ __('No chat history available.') }}
                        </div>
                    </div>
                @endforelse
            </div>
        </ul>
    </div>
</aside>

