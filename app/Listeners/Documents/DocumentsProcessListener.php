<?php

namespace App\Listeners\Documents;

use App\Actions\Tenant\Backend\CategoriesExtraction;
use App\Events\Documents\DocumentProcessEvent;
use App\Facade\LlmManagerFacade;
use App\Handlers\DocumentProcessingHandler;
use App\Http\Middleware\AuthenticateQueuesMiddleware;
use App\Models\Source;
use App\Responses\HuggingFace\HuggingFaceResponse;
use App\Serializers\ClosureSerializer;
use App\Services\Documents\TextExtractor;
use App\Services\Extractors\SummaryExtractor;
use AssistedMindfulness\Rake\Rake;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Exception;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Laravel\SerializableClosure\Exceptions\PhpVersionNotSupportedException;
use Throwable;

class DocumentsProcessListener implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, InteractsWithSockets, Queueable;

    /**
     * Handle the event.
     *
     * @throws Throwable
     */
    public function handle(DocumentProcessEvent $event): void
    {
        $source = $event->source;
        try {

            $data = $this->prepareData($event);
            $this->getContentAndTags($source);

            $beforeCommit = function (string $name) use ($source): void {
                $source->update(['job_batch_id' => $name]);
            };

            $finalCallback = function () use ($data): void {
                unlink($data['file']);
            };

            $serializedBeforeCommit = $this->serializeClosure($beforeCommit);
            $serializedFinalCallback = $this->serializeClosure($finalCallback);

            $documentHandler = new DocumentProcessingHandler($event->provider);

            if ($documentHandler->validate($data)) {
                $documentHandler->handle(
                    data: $data,
                    beforeCommit: $serializedBeforeCommit,
                    finalCallback: $serializedFinalCallback
                );
            }
        } catch (Throwable $e) {
            Log::error('Document Processing Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Prepare the data array from the event.
     *
     * @throws Exception
     */
    private function prepareData(DocumentProcessEvent $event): array
    {
        return [
            'file' => $this->getFile($event->source),
            'model' => $event->source,
        ];
    }

    /**
     * @throws Exception
     */
    public function getFile(Model $model): string
    {
        // $disk = config('filesystems.default');
        // $filePath = $model->file->filepath;
        // if (! Storage::disk($disk)->exists($filePath)) {
        //     throw new Exception("File does not exist on disk: {$disk}, path: {$filePath}");
        // }

        // return Storage::disk($disk)->path($filePath);
        return storage_path('app/' . $model->file->filepath);
    }

    public function getContentAndTags($source)
    {
        $textExtractor = new TextExtractor;
        $content = $textExtractor->extractText($this->getFile($source));
        $stupidContext = $this->generateContent($content);
        $tags=$this->getTags($stupidContext);
        $source->syncTags($tags);
        $summary= $this->getSummarize($stupidContext);
        $source->update(['content'=> $summary]);
        $this->getHub($source);
    }


    public function getSummarize($context)
    {
        $summaryExractor = new SummaryExtractor;
        $prompt = $summaryExractor->handle($context);
        return $this->getResponse($prompt, 100);
    }


    public function getHub($source)
    {
        if (!$source->hub_id) {

            $hub_id = CategoriesExtraction::run($source->content);
            if($hub_id)
            $source->update(['hub_id'=> $hub_id]);
        }
    }


    public function getResponse($prompt, $token = 300): string
    {
        $response =  LlmManagerFacade::build(config('llm.config'));

        return $response->getResponse($prompt);
    }


    public function getTags($context)
    {
        $rake = new Rake(4, false);
        return $rake->extract($context)->sortByScore('desc')->keywords();
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

    /**
     * Serialize a closure.
     *
     * @throws PhpVersionNotSupportedException
     */
    private function serializeClosure(callable $closure): string
    {
        return ClosureSerializer::serialize($closure);
    }

    public function midlleware(DocumentProcessEvent $event)
    {
        return [new AuthenticateQueuesMiddleware($event->user_id)];
    }
}
