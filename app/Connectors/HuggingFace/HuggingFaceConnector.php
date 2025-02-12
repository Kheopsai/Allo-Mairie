<?php

namespace App\Connectors\HuggingFace;

use Saloon\Http\Connector;

class HuggingFaceConnector extends Connector
{
    public function resolveBaseUrl(): string
    {
        return config('services.kheops.url');
    }

    public function defaultConfig():array
    {
        return [
            'stream' => true
        ];
    }
}
