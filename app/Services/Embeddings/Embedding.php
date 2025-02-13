<?php

namespace App\Services\Embeddings;

use App\Responses\Ollama\OllamaEmbeddingResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class Embedding
{
    use AsAction;
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public static function handle($splits):array
    {
        $embed = new OllamaEmbeddingResponse('bge-m3',$splits);
        return $embed->getEmbedding();
    }
}
