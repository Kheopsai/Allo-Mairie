<?php

namespace App\Jobs\Sources;

use App\Enums\StatusEnum;
use App\Enums\VectorStoreEnum;
use App\Jobs\Chats\ProcessDocumentSplitJob;
use App\Models\Source;
use App\Models\Tenant;
use App\Services\TextSplitter\TextSplit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Bus\Batch as BusBatch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessDocumentJob implements ShouldQueue
{
    use Dispatchable;
    use Queueable;

    protected Source $source;

    public Tenant $tenant;

    /**
     * Create a new job instance.
     */
    public function __construct(Tenant $tenant, Source $source)
    {
        $this->source = $source;
        $this->tenant = $tenant;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $textSplit = new TextSplit;
            $splits = $textSplit->fromFile($this->source->file->path());

            $jobs = [];
            $context = '';
            foreach ($splits as $split) {
                $context .= $split;
                $jobs[] = new ProcessDocumentSplitJob(
                    text: (string) $split,
                    provider: VectorStoreEnum::Postgres,
                    params: ['model' => $this->source]
                );
            }

            $sourceId = $this->source->id;

            Bus::batch($jobs)
                ->name('Process Document Splits for Source '.$sourceId)
                ->then(function (BusBatch $batch) use ($context) {
                    $this->source->update(['context' => $context, 'job_batch_id' => $batch->id]);
                    Log::info('Batch started successfully', ['batch_id' => $batch->id]);
                })
                ->catch(function (BusBatch $batch, Throwable $e) {
                    Log::error('Error in batch processing', [
                        'batch_id' => $batch->id,
                        'exception' => $e->getMessage(),
                    ]);
                })
                ->finally(function (BusBatch $batch) use ($sourceId) {
                    $source = Source::find($sourceId);

                    if ($source) {
                        $source->update(['status' => StatusEnum::SUCCESS]);
                        $source->removeAllFiles();
                    }
                })
                ->dispatch();
        } catch (Throwable $e) {
            Log::error('Error processing uploaded file', ['exception' => $e]);
        }
    }
}
