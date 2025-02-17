<?php

namespace App\Listeners;

use App\Actions\Tenant\Backend\Tags;
use App\Enums\StatusEnum;
use App\Events\ScrapperProcessEvent;
use App\Models\Source;
use App\Parsers\HtmlToText;
use App\Responses\HuggingFace\HuggingFaceResponse;
use App\Services\Extractors\SummaryExtractor;
use App\Services\Scrapper\Scrapper;
use App\Services\TextSplitter\TextSplit;
use App\Services\VectorStores\PostgresVectorStore;
use AssistedMindfulness\Rake\Rake;
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
    protected Tags $tagExtractor;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        $this->textSplit = new TextSplit;
        $this->vectorStore = new PostgresVectorStore;
        $this->tagExtractor = new Tags;
    }

    /**
     * Handle the event.
     */
    public function handle(ScrapperProcessEvent $event): void
    {
        $id= $event->id;

        $scrapper = new Scrapper($event->url);
        $html = $scrapper->handle();
        $formater = new HtmlToText($html);
        $clear = $formater->formatContent(['a', 'svg', 'button']);
        $content = $formater->toPlainText($clear);
        $splits = $this->textSplit->handle($content);

        foreach ($splits as $split) {
            $this->vectorStore->init(['id' => $id,'model'=> Source::find($id)]);
            $this->vectorStore->addText($split);
            Source::findOrFail($id)->update(['status' => StatusEnum::SUCCESS]);
        }

        // $this->getContentAndTags($id,$content);
    }

    public function getContentAndTags($id,$content)
    {

        $source = Source::findOrFail($id);
        $tags=$this->getTags($content);
        ds($content);
        $source->syncTags($tags);
        $summary= $this->getSummarize($content);
        ds($summary);
        $source->update(['content'=> $summary]);
    }


    public function estimateTokenCount($text): float
    {
        return ceil(strlen($text) / 2);
    }

    public function concatenateContextsWithLimit(array $contexts, $maxTokens = 4700): string
    {
        $concatenatedContext = '';
        $currentTokenCount = 0;
        foreach ($contexts as $context) {
            $context = preg_replace('/\s+/', ' ', trim($context));
            $contextTokenCount = $this->estimateTokenCount($context);
            if ($currentTokenCount + $contextTokenCount > $maxTokens) {
                break;
            }

            $concatenatedContext .= $context . "\n";
            $currentTokenCount += $contextTokenCount;
        }

        return trim($concatenatedContext);
    }

    public function generateContent($content): string
    {
        return $this->concatenateContextsWithLimit($content);
    }


    public function getSummarize($context)
    {
        $summaryExractor = new SummaryExtractor;
        $prompt = $summaryExractor->handle($context);
        return $this->getResponse($prompt, 100);
    }


    public function getResponse($prompt, $token = 300): string
    {
        $response = new HuggingFaceResponse($prompt, $token);

        return $response->getGeneratedText();
    }

    public function getTags($context)
    {
        $rake = new Rake(4, false);
        return $rake->extract($context)->sortByScore('desc')->keywords();
    }

    // public function failed(ScrapperProcessEvent $event, Throwable $exception): void
    // {
    //     $id = $event->id;
    //     Source::findOrFail($id)->update(['status' => StatusEnum::ERROR]);
    // }
}
