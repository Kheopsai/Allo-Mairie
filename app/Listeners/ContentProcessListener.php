<?php

namespace App\Listeners;

use App\Actions\Tenant\Backend\CategoriesExtraction;
use App\Actions\Tenant\Backend\Tags;
use App\Enums\StatusEnum;
use App\Events\ContentProcessEvent;
use App\Facade\LlmManagerFacade;
use App\Models\Hub;
use App\Models\Source;
use App\Services\CategoriesExtractor\CategoriesExtractor;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\TextSplitter\TextSplit;
use App\Services\VectorStores\PostgresVectorStore;
use AssistedMindfulness\Rake\Rake;
use Exception;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Throwable;

class ContentProcessListener implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, InteractsWithSockets, Queueable, SerializesModels;

    public PostgresVectorStore $vectorStore;

    protected TextSplit $textSplit;

    protected Tags $tagExtractor;

    protected CategoriesExtractor $categoriesExtractor;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        $this->textSplit = new TextSplit;
        $this->vectorStore = new PostgresVectorStore;
        $this->tagExtractor = new Tags;
        $this->categoriesExtractor = new CategoriesExtractor;
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

        $splits = $this->textSplit->handle($text);

        foreach ($splits as $split) {
            $this->vectorStore->init(['id' => $id, 'model' => Source::find($id)]);
            $this->vectorStore->addText($split);
            Source::findOrFail($id)->update(['status' => StatusEnum::SUCCESS]);
        }
        $tags = $this->getTags($this->tagExtractor->handle($text));
        $this->getHub($event);
        Source::findOrFail($id)->syncTags($tags);
    }

    public function getHub(ContentProcessEvent $event)
    {
        if (!Source::findOrFail($event->id)->hub_id) {

            $hub_id = CategoriesExtraction::run($event->text);
            if($hub_id)
            Source::findOrFail($event->id)->update(['hub_id'=> $hub_id]);
        }
    }


    public function getTags($context)
    {
        $rake = new Rake(4, false);
        return $rake->extract($context)->sortByScore('desc')->keywords();
    }

    public function failed(ContentProcessEvent $event, Throwable $exception): void
    {
        $id = $event->id;
        Source::findOrFail($id)->update(['status' => StatusEnum::ERROR]);
    }
}
