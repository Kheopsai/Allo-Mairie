<?php

namespace App\Services\Llm;

use App\Interface\LlmServiceProviderInterface;
use Generator;
use HelgeSverre\Mistral\Enums\Model;
use HelgeSverre\Mistral\Mistral;
use Illuminate\Support\Str;

class MistralService implements LlmServiceProviderInterface
{
    protected string $generated = '';

    protected Mistral $client;

    public float $conversion_rate = 80;

    public function __construct()
    {
        $this->client = new Mistral(apiKey: config('mistral.api_key'));
    }

    /**
     * Retrieve the generated text.
     *
     * @return string The generated text.
     */
    public function getGeneratedText(): string
    {
        return $this->generated;
    }

    /**
     * Set the generated text.
     *
     * @param  string  $generated  The text to set.
     */
    public function setGenerated(string $generated): void
    {
        $this->generated .= $generated;
    }

    /**
     * Process the input message and generate responses iteratively.
     *
     * @param  string  $message  The input message.
     * @return Generator Yields generated parts or the entire item based on conditions.
     */
    public function getIteration(string $message): Generator
    {
        $stream = $this->client->chat()->createStreamed(
            messages: [['role' => 'user', 'content' => $message]],
            model: Model::large->value
        );

        foreach ($stream as $chunk) {
            $this->setGenerated(Str::of($chunk->choices[0]->delta->content)->toString());
            yield Str::of($chunk->choices[0]->delta->content)->toString();
        }
    }

    /**
     * Get the response text for a given message.
     *
     * @param  string  $message  The message to process.
     * @return string The processed response text.
     *
     * @throws Exception
     */
    public function getResponse(string $message): string
    {
        $response = $this->client->chat()->create(
            messages: [['role' => 'user', 'content' => $message]],
            model: Model::large->value
        );

        return $response->dto()->choices[0]->message->content;
    }
}
