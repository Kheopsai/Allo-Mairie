<?php

namespace App\Jobs\Chats;

use App\Facade\VectorStoreFacade;
use Exception;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use JsonException;

class ProcessDocumentSplitJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable;

    protected string $text;

    protected string $provider;

    protected $params;

    public function __construct(string $text, string $provider, $params)
    {
        $this->text = $text;
        $this->provider = $provider;
        $this->params = $params;
    }

    /**
     * @throws JsonException
     * @throws Exception
     */
    public function handle(): void
    {
        $vectorStore = VectorStoreFacade::build($this->provider)->init($this->params);
        $vectorStore->addText($this->text);
    }
}
