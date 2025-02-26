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
use Illuminate\Support\Facades\Cache;
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

    public string $message='';

    public string $generatedMessage;

    public bool $isAble = false;


    public $sources;


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
        if(empty($this->message))return ;
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
        $cacheKey = $this->getCacheKey();
        if (Cache::has($cacheKey)) {
            $cachedResponse = Cache::get($cacheKey);
            $this->documents = $cachedResponse['documents'];
        }
        $embedding = Embedding::handle($this->message);

        $hub_id = CategoriesExtraction::run($this->message,true);

        if ($hub_id)
            $contexts = Hub::findOrFail($hub_id)->vectorStores()->nearestNeighbors('embedding', $embedding, Distance::Cosine)->get()->toArray();
        else
            $contexts = [];

        $contextService = new ContextService($this->message, $contexts);
        $context = $contextService->search()->rerank()->pluck('text')->concatenateContextsWithLimit();
        $cacheKey = $this->getCacheKey();

        $this->sources = collect($contextService->getSelectedDocuments())
            ->map(function ($doc) {
                return is_array($doc) ? $doc : $doc;
            })
            ->toArray();
        Cache::put($cacheKey, ['documents' => $this->sources], now()->addHours(config('cache.chat_ttl', 1)));
        $this->generatedMessage = \App\Actions\Tenant\Backend\Chat::handleStatic($this->message, $context);
        $this->reset('message');
    }

    private function getCacheKey(): string
    {
        return 'channel:' . md5($this->channel);
    }


    private function dispatchSummaryExtractor($message): void
    {
        if (! empty($this->channel)) {

            if (Channel::find($this->channel)) {
                SummaryExtractorEvent::dispatch(Channel::find($this->channel), $message, 'name');
            }
        }
    }

    #[Computed]
    public function getSafeDocuments(): array
    {
        if (isset($this->channel)) {
            if (Cache::has($this->getCacheKey())) {
                $cachedResponse = Cache::get($this->getCacheKey());
                $this->sources = $cachedResponse['documents'];
            }
        }
        return collect($this->sources)
            ->map(function ($doc) {
                return [
                    'content' => $doc ?? '',
                    'score' => $doc['score'] ?? null
                ];
            })
            ->toArray();
    }

    #[Computed]
    function renderMixedContent($content): string
    {
        $content = preg_replace('/(\n)\s*\./', '$1', $content);
        $parts = preg_split('/(<.*?>)/', $content, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        $output = '';
        foreach ($parts as $part) {
            if (preg_match('/<.*?>/', $part)) {
                $output .= $part;
            } else {
                $processed = str()->inlineMarkdown(preg_replace(['/\n{2,}/', '/\n/'], ["\n\n", "  \n"], $part));
                $output .= preg_replace('/(<br>\s*|<\/p>\s*<p>\s*)\./', '$1', $processed);
            }
        }

        return $output;
    }
}
