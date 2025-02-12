<?php

namespace App\Listeners\Extractors;

use App\Events\Extractors\SummaryExtractorEvent;
use App\Facade\LlmManagerFacade;
use App\Services\Extractors\SummaryExtractor;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SummaryExtractorListener implements ShouldQueue
{
    use Dispatchable,InteractsWithQueue,InteractsWithSockets,Queueable,SerializesModels;

    public SummaryExtractor $extractor;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        $this->extractor = new SummaryExtractor;
    }

    /**
     * Handle the event.
     */
    public function handle(SummaryExtractorEvent $event): void
    {
        $prompt = $this->extractor->handle($event->text);
        $response= LlmManagerFacade::build(config('llm.config'));
        $model= $event->model;
        $model->{$event->input}= $response->getResponse($prompt) ;
        $model->save();
    }
}
