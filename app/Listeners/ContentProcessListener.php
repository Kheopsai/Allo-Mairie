<?php

namespace App\Listeners;

use App\Events\ContentProcessEvent;
use App\Services\PostgresVector\PostgresVector;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\TextSplitter\TextSplit;
use App\Services\VectorStores\ElasticSearch\ElasticSearchVectorStore;
use Exception;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Throwable;

class ContentProcessListener implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, InteractsWithSockets, Queueable, SerializesModels;

    public PostgresVector $vectorStore;

    protected TextSplit $textSplit;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        $this->textSplit = new TextSplit;
        $this->vectorStore = new PostgresVector;
    }

    /**
     * Handle the event.
     *
     * @throws Exception
     */
    public function handle(ContentProcessEvent $event): void
    {
        $id = $event->id;
        $text = $event->text;
        $collection = $event->collection;

        $splits = $this->textSplit->handle($text);

        foreach ($splits as $split) {
            $this->vectorStore->init(['id' => $id, 'collection' => $collection]);
            $this->vectorStore->addText($split);
            Hub::findOrFail($id)->update(['status' => StatusType::SUCCESS]);
        }
    }

    public function failed(ContentProcessEvent $event, Throwable $exception): void
    {
        $id = $event->id;
        Hub::findOrFail($id)->update(['status' => StatusType::ERROR]);
    }
}
