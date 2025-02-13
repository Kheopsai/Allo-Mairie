<?php

namespace App\Requests\HuggingFace;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class HuggingFaceEmbeddingRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method =Method::POST;

    public function __construct(protected string $inputes)
    {

    }

    public function resolveEndpoint(): string
    {
        return '/';
    }

    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept'=> 'application/json'
        ];
    }

    protected function defaultBody(): array
    {
        return [
            'inputs'=> $this->inputes
        ];
    }


}
