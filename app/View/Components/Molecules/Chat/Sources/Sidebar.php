<?php

namespace App\View\Components\Molecules\Chat\Sources;

use App\Models\Channel;
use App\Traits\HasChat;
use App\View\Pages\Tenant\Backend\Chat\Index;
use Livewire\Attributes\Isolate;
use Livewire\Component;

#[Isolate]
class Sidebar extends Component
{

    use HasChat;

    public $channels = [];

    public function mount() {
        $this->loadChannels();
    }

    public function loadChannels()
    {
        $this->channels = Channel::where('user_id', auth()->id())->latest()->get()->toArray();
    }

    public function loadChannel(string $channelId): void
    {
        $this->dispatch('loadChannel', channel: $channelId);
        $this->dispatch('loadMessages');
    }

    public function newChannel(): void
    {
        $this->reset();
        $this->dispatch('resetAll')->component(Index::class);
    }

    public function confirmDeleteChannel(string $channelId): void
    {
        $this->dialog()->confirm([
            'title' => __('Are you sure?'),
            'description' => __('Delete the selected resource'),
            'icon' => 'error',
            'acceptLabel' => __('Yes, delete it'),
            'method' => 'deleteChannel',
            'params' => $channelId,
        ]);
    }

    public function deleteChannel(string $channelId): void
    {
        $channel = Channel::findOrFail($channelId);
        $channel->delete();

        $this->loadChannels();

        $this->notification()->success(
            $title = __('Channel Deleted'),
            $description = __('The channel has been successfully deleted.')
        );
    }

    public function refreshChannels(): void
    {
        $this->loadChannels();
    }

    public function render()
    {
        return view('components.molecules.chat.sources.sidebar');
    }
}
