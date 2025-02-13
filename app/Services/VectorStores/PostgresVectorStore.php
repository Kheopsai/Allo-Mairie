<?php

namespace App\Services\VectorStores;

use App\Interface\VectorStoreInterface ;
use App\Models\VectorStore;
use App\Services\Embeddings\Embedding;
use App\Services\PostgresVector\PostgresVector;
use App\Services\TextSplitter\TextSplit;
use Exception;
use Lorisleiva\Actions\Concerns\AsAction;
use SplFileInfo;

class PostgresVectorStore implements VectorStoreInterface
{
    use AsAction;

    private array $collections;

    private Embedding $embedding;

    private PostgresVector $storage;

    private array $options;

    /**
     * Adds a document to the PostgresVector store while ensuring required parameters.
     *
     * @param  string|SplFileInfo  $documentIdentifier  Typically a SplFileInfo instance for file-based operations.
     *
     * @throws Exception if the required parameters are not provided or incorrect.
     */
    public function addDocument(string|SplFileInfo $documentIdentifier): void
    {
        $textSplit = new TextSplit;
        foreach ($textSplit->fromFile($documentIdentifier) as $split) {
            $this->addText(text: (string) $split);
        }
    }

    /**
     * @throws Exception
     */
    public function addText(string $text): void
    {
        $vectorStore = new VectorStore;
        $vectorStore->embedding = Embedding::handle($text);
        $vectorStore->text = $text;
        $this->storage->save($vectorStore);
    }

    public function getDocumentPool(): array
    {
        return $this->collections;
    }

    public function init($params): VectorStoreInterface
    {
        $this->embedding = new Embedding;
        $this->storage = new PostgresVector($params['model']);

        return $this;
    }

    public function addTexts(array $texts): void
    {
        foreach ($texts as $text) {
            $this->addText($text);
        }
    }
}
