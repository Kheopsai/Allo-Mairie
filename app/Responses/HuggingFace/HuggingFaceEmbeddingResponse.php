<?php

namespace App\Responses\HuggingFace;

use App\Connectors\HuggingFace\HuggingFaceEmbeddingConnector;
use App\Requests\HuggingFace\HuggingFaceEmbeddingRequest;
use Exception;

class HuggingFaceEmbeddingResponse
{

    public HuggingFaceEmbeddingConnector $connection;
    public HuggingFaceEmbeddingRequest $request;

    public function __construct($inputs)
    {
        $this->connection = new HuggingFaceEmbeddingConnector;
        $this->request = new HuggingFaceEmbeddingRequest($inputs);
    }

    public function getEmbedding(): array
    {
        try {
            $response = $this->connection->send($this->request)->body();
            $embedding = json_decode($response, true);
            return $embedding['embeddings'];
        } catch (Exception $e) {
            throw new Exception('An error occurred while retrieving generated text: ' . $e->getMessage());
        }
    }
}
