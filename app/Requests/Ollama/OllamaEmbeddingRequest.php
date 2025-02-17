<?php

namespace App\Requests\Ollama;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class OllamaEmbeddingRequest extends Request implements HasBody
{
    use HasJsonBody;

    public Method $method = Method::POST;


    public function __construct(protected string $model,protected string $input)
    {

    }

    public function resolveEndpoint(): string
    {
      return  '/api/embed';
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
            'model'=> $this->model,
            'input'=> $this->input
        ];
    }

}
