<?php

namespace App\Traits;

use App\Actions\Tenant\Backend\CategoriesExtraction;
use App\Enums\ChatType;
use App\Enums\SenderEnum;
use App\Events\Extractors\SummaryExtractorEvent;
use App\Models\Channel;
use App\Models\Chat;
use App\Models\Hub;
use App\Models\VectorStore;
use App\Services\Context\ContextService;
use App\Services\Embeddings\Embedding;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Pgvector\Laravel\Distance;

trait HasChat
{
    #[Url()]
    public ?string $channel = null;

    public Collection $chats;

    public bool $isLoading = false;

    public bool $isStart = true;

    public string $message;

    public string $generatedMessage;

    public bool $isAble = false;


    public function updatedMessage(): void
    {
        if (empty($this->message)) {
            $this->isAble = false;
        } else {
            $this->isAble = true;
        }
    }


    public function boot(): void
    {
        if (! empty($this->channel)) {
            if (Channel::find($this->channel)) {
                $this->chats = Channel::findOrFail($this->channel)->chats()->orderBy('created_at')->get();
            } else {
                $this->chats = collect();
                $this->reset('channel');
            }
        } else {
            $this->chats = collect();
        }
    }

    public function toggleEditable(): void
    {
        $this->dispatch('toggleEditable');
    }

    #[On('loadChannel')]
    public function loadChannel($channel): void
    {
        $this->channel = $channel;
    }

    #[On('loading')]
    public function toggleLoading(): void
    {
        $this->isLoading = ! $this->isLoading;
    }
    /**
     * @throws Exception
     */
    private function store(?string $message, $sender = ChatType::Assistant): Chat
    {
        $this->createNewChannel();
        $chat = Chat::create([
            'id' => Str::uuid()->toString(),
            'sender' => $sender,
            'message' => empty($message) ? trans('Server Error') : $message,
            'channel_id' => $this->channel,
        ]);
        $this->loadMessages();
        $this->reset('isLoading');

        return $chat;
    }

    private function createNewChannel(): void
    {
        if (empty($this->channel)) {
            $name = Str::uuid()->toString();
            Channel::create([
                'id' => $name,
                'name' => trans('Untitled document'),
                'user_id' => Auth::id(),
            ]);
            $this->channel = $name;
            $this->dispatch('loadChannel', $name);
        }
    }

    #[Computed]
    public function groupedChats()
    {
        return $this->chats->sortBy('created_at')->groupBy(function ($message) {
            return $message->created_at->format('Y-m-d');
        });
    }

    /**
     * @throws DdException
     */
    #[On('chat')]
    public function createMessage(): void
    {
        $this->dispatchSummaryExtractor($this->message);
        $this->buildContext();
        $this->toggleLoading();
        $this->js('$wire.askKheops(true)');
    }

    /**
     * @throws Exception
     */
    private function sendMessage(): void
    {
        $this->createNewChannel();
        if (! empty($this->message)) {
            Chat::create([
                'id' => Str::uuid()->toString(),
                'sender' => ChatType::User,
                'message' => $this->message,
                'channel_id' => $this->channel,
            ]);
            $this->loadMessages();
        }
        $this->reset('isAble');
    }

    #[On('loadMessages')]
    public function loadMessages(): void
    {
        if (Channel::find($this->channel)) {
            $this->chats = Channel::findOrFail($this->channel)->chats()->orderBy('created_at')->get();
        }
    }


    /**
     * @throws DdException
     * @throws Exception
     */
    public function buildContext(): void
    {
        $embedding = Embedding::handle($this->message);

        $hub_id = CategoriesExtraction::run($this->message);

        if ($hub_id)
            $contexts = Hub::findOrFail($hub_id)->vectorStores()->nearestNeighbors('embedding', $embedding, Distance::Cosine)->get()->toArray();
        else
            $contexts = [];

        $contextService = new ContextService($this->message, $contexts);
        $context = $contextService->search()->rerank()->pluck('text')->concatenateContextsWithLimit();
        $this->generatedMessage = \App\Actions\Tenant\Backend\Chat::handleStatic($this->message, $context);
        $this->reset('message');
    }


    private function dispatchSummaryExtractor($message): void
    {
        if (! empty($this->channel)) {

            if (Channel::find($this->channel)) {
                SummaryExtractorEvent::dispatch(Channel::find($this->channel), $message, 'name');
            }
        }
    }
}
