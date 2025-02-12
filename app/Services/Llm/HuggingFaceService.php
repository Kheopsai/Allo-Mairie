<?php

namespace App\Services\Llm;

use App\Interface\LlmServiceProviderInterface;
use App\Responses\HuggingFace\HuggingFaceResponse;
use App\Responses\HuggingFace\HuggingFaceStreamResponse;
use Illuminate\Support\Str;
use Generator;

class HuggingFaceService implements LlmServiceProviderInterface
{
    protected string $generated = '';
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
        $this->generated = $generated;
    }

    /**
     * Process the input message and generate responses iteratively.
     *
     * @param  string  $message  The input message.
     * @param  string  $stopParameter  Parameters that control the stopping condition of the iteration.
     * @return Generator Yields generated parts or the entire item based on conditions.
     *
     * @throws FatalRequestException
     * @throws JsonException
     * @throws RequestException
     * @throws Throwable
     */
    public function getIteration(string $message, string $stopParameter = '<|im_end|>'): Generator
    {
        $stream = new HuggingFaceStreamResponse($message);
        $content = '';

        foreach ($stream->getIteration() as $item) {
            if (isset($item['token']['text'])) {
                $tokenText = $item['token']['text'];
                $content .= $tokenText;

                if (Str::contains($content, $stopParameter)) {
                    $this->setGenerated(Str::of($content)->remove($stopParameter)->toString());
                    break;
                }

                yield Str::of($tokenText)->remove($stopParameter)->toString();
            } else {
                yield $item;
            }
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
        $response = new HuggingFaceResponse($message);

        return $response->getGeneratedText();
    }
}
