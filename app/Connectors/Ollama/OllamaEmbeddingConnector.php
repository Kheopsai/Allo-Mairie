<?php

namespace App\Connectors\Ollama;

use Saloon\Http\Connector;

class OllamaEmbeddingConnector extends Connector
{
    public function resolveBaseUrl(): string
    {
        return config('ollam-laravel.embed');
    }

    public function defaultConfig():array
    {
        return [];
    }

    public function defaultDelay(): ?int
    {
        return 500;
    }

}
