<?php

namespace App\Responses\Ollama;

use App\Connectors\Ollama\OllamaEmbeddingConnector;
use App\Requests\Ollama\OllamaEmbeddingRequest;
use Exception;

class OllamaEmbeddingResponse
{

    public OllamaEmbeddingConnector $connection;

    public OllamaEmbeddingRequest $request;


    public function __construct($model,$inputs)
    {
        $this->connection=new OllamaEmbeddingConnector;
        $this->request = new OllamaEmbeddingRequest($model,$inputs);
    }

    public function getEmbedding():array
    {
        try {
            $response = $this->connection->send($this->request)->body();
            $embedding= json_decode($response,true);
            return $embedding['embedding'][0];
        } catch (Exception $e) {
            throw new Exception('An error occurred while retrieving generated text: '.$e->getMessage());
        }
    }
}
