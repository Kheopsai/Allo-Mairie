<?php

namespace App\Listeners;

use App\Enums\StatusEnum;
use App\Events\ScrapperProcessEvent;
use App\Models\Source;
use App\Services\Scrapper\Scrapper;
use App\Services\TextSplitter\TextSplit;
use App\Services\VectorStores\PostgresVectorStore;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ScrapperProcessListener implements ShouldQueue
{

    use Dispatchable, InteractsWithQueue, InteractsWithSockets, Queueable, SerializesModels;

    public TextSplit $textSplit;
    public PostgresVectorStore $vectorStore;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        $this->textSplit = new TextSplit;
        $this->vectorStore = new PostgresVectorStore;
    }

    /**
     * Handle the event.
     */
    public function handle(ScrapperProcessEvent $event): void
    {
        $id= $event->id;

        $scrapper = new Scrapper($event->url);
        $content = $scrapper->handle();
        $splits = $this->textSplit->handle($content);

        foreach ($splits as $split) {
            $this->vectorStore->init(['id' => $id,'model'=> Source::find($id)]);
            $this->vectorStore->addText($split);
            Source::findOrFail($id)->update(['status' => StatusEnum::SUCCESS]);
        }
    }


    // public function failed(ScrapperProcessEvent $event, Throwable $exception): void
    // {
    //     $id = $event->id;
    //     Source::findOrFail($id)->update(['status' => StatusEnum::ERROR]);
    // }
}
