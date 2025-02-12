<?php

namespace App\Connectors\HuggingFace;

use Saloon\Http\Connector;

class HuggingFaceEmbeddingConnector extends Connector
{
    public function resolveBaseUrl(): string
    {
        return config('services.kheops.embedding');
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
