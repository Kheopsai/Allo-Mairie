<?php

namespace App\Listeners\Documents;

use App\Events\Documents\DocumentProcessEvent;
use App\Handlers\DocumentProcessingHandler;
use App\Serializers\ClosureSerializer;
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
            Log::error('Document Processing Error: '.$e->getMessage());
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
        return storage_path('app/'.$model->file->filepath);
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
}
