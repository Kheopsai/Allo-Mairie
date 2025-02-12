<?php

namespace App\Responses\HuggingFace;

use App\Connectors\HuggingFace\HuggingFaceConnector;
use App\Requests\HuggingFace\HuggingFaceStreamRequest;
use Generator;

class HuggingFaceStreamResponse
{

    public HuggingFaceConnector $connection;
    public HuggingFaceStreamRequest $request;

    public function __construct($inputs, $parameters = [])
    {
        $this->connection = new HuggingFaceConnector();
        $this->request = new HuggingFaceStreamRequest($inputs, $parameters = []);
    }

    /**
     * @throws FatalRequestException
     * @throws RequestException
     * @throws JsonException
     * @throws Throwable
     */
    public function getIteration(array $stopParameters = ['stop' => ['<|im_end|>']]): Generator
    {
        $response = $this->connection->send($this->request);
        if (! $response->successful()) {
            yield json_decode($response->body(), true, flags: JSON_THROW_ON_ERROR);

            return;
        }
        $stream = $response->stream();
        while (! $stream->eof()) {
            $line = $this->readLine($stream);
            if (! str_starts_with($line, 'data:')) {
                continue;
            }

            $data = trim(substr($line, strlen('data:')));

            if (in_array($data, $stopParameters['stop'], true)) {
                break;
            }

            yield json_decode($data, true, flags: JSON_THROW_ON_ERROR);
        }
    }

    protected function readLine($stream): string
    {
        $buffer = '';
        while (! $stream->eof()) {
            if (($byte = $stream->read(1)) === '') {
                return $buffer;
            }
            $buffer .= $byte;
            if ($byte === "\n") {
                break;
            }
        }

        return $buffer;
    }
}
