<?php

namespace App\Responses\HuggingFace;

use App\Connectors\HuggingFace\HuggingFaceConnector;
use App\Requests\HuggingFace\HuggingFaceRequest;
use Exception;

class HuggingFaceResponse
{
    public HuggingFaceConnector $connection;
    public HuggingFaceRequest $request;

    public function __construct($inputs, $token = 2400, $parameters = [])
    {
        $this->connection = new HuggingFaceConnector();
        $this->request = new HuggingFaceRequest($inputs, $token, $parameters);
    }

    /**
     * Retrieves generated text from a remote service.
     *
     * @return string The generated text from the response.
     *
     * @throws Exception If the request fails or the response cannot be processed.
     */
    public function getGeneratedText(): string
    {
        try {
            $responseBody = $this->connection->send($this->request)->body();
            $decodedResponse = json_decode($responseBody, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                if (isset($decodedResponse[0]['generated_text'])) {
                    return $decodedResponse[0]['generated_text'];
                }
                throw new Exception('Missing \'generated_text\' in the response.');
            } else {
                throw new Exception('Failed to decode JSON: ' . json_last_error_msg());
            }
        } catch (Exception $e) {
            throw new Exception('Error retrieving generated text: ' . $e->getMessage());
        }
    }
}
