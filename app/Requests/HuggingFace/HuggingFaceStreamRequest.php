<?php

namespace App\Requests\HuggingFace;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class HuggingFaceStreamRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(protected string $inputs, protected array $parameters = []) {}

    public function resolveEndpoint(): string
    {
        return '/';
    }

    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    protected function defaultBody(): array
    {
        return [
            'inputs' => $this->inputs,
            'parameters' => [
                'max_new_tokens' => 2400,
                'top_k' => 10,
                'top_p' => 0.95,
                'typical_p' => 0.95,
                'temperature' => 0.01,
                'repetition_penalty' => 1.03,
                'return_tensors' => false,
                'streaming' => false,
                'do_sample' => false,
                'return_full_text' => false,
            ],
            'stream' => true,
        ];
    }
}
