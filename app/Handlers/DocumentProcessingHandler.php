<?php

namespace App\Handlers;

use App\Interface\ActionHandlerInterface;
use App\Enums\VectorStoreEnum;
use App\Jobs\Chats\ProcessDocumentSplitJob;
use App\Serializers\ClosureSerializer;
use App\Services\TextSplitter\TextSplit;
use Closure;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Str;
use Throwable;

class DocumentProcessingHandler implements ActionHandlerInterface
{
    protected TextSplit $textSplit;

    protected string $provider;

    public function __construct(string $provider = VectorStoreEnum::Postgres)
    {
        $this->textSplit = new TextSplit;
        $this->provider = $provider;
    }

    public function validate($data): bool
    {
        return isset($data['file']);
    }

    /**
     * @throws Throwable
     */
    public function handle($data, $successCallback = null, $beforeCommit = null, $failedCommit = null, $finalCallback = null): void
    {
        $this->process($data, $successCallback, $beforeCommit, $failedCommit, $finalCallback);
    }

    /**
     * @throws Throwable
     */
    public function process($data, $successCallback = null, $beforeCommit = null, $failedCommit = null, $finalCallback = null): bool
    {
        $successCallback = $successCallback ? $this->unserialize($successCallback) : null;
        $beforeCommit = $beforeCommit ? $this->unserialize($beforeCommit) : null;
        $failedCommit = $failedCommit ? $this->unserialize($failedCommit) : null;
        $finalCallback = $finalCallback ? $this->unserialize($finalCallback) : null;
        $jobs = [];

        $splits = $this->textSplit->fromFile($data['file']);

        foreach ($splits as $split) {
            $jobs[] = new ProcessDocumentSplitJob(text: (string) $split, provider: $this->provider, params: $data);
        }
        $name = Str::uuid()->toString();

        if ($beforeCommit) {
            call_user_func($beforeCommit, $name);
        }

        Bus::batch($jobs)
            ->name($name)
            ->then(function () use ($successCallback, $data) {
                if ($successCallback) {
                    call_user_func($successCallback, $data);
                }
            })
            ->catch(function () use ($failedCommit, $data) {
                if ($failedCommit) {
                    call_user_func($failedCommit, $data);
                }
            })
            ->finally(function () use ($finalCallback, $data) {
                if ($finalCallback) {
                    call_user_func($finalCallback, $data);
                }
            })
            ->dispatch();

        return true;
    }

    public function unserialize($closure): Closure
    {
        return ClosureSerializer::unserialize($closure);
    }
}
