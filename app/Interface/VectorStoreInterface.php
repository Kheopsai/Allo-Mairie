<?php

namespace App\Interface;

use Exception;
use SplFileInfo;

interface VectorStoreInterface
{
    /**
     * Retrieves collections or documents pool.
     */
    public function init($params);

    /**
     * Adds a document to the store.
     *
     * @param  string|SplFileInfo  $documentIdentifier  Identifier of the document (e.g., text or file path).
     *
     * @throws Exception
     */
    public function addDocument(string|SplFileInfo $documentIdentifier): void;

    /**
     * Adds a document to the store.
     *
     * @param  string  $text  Identifier of the document (e.g., text or file path).
     *
     * @throws Exception
     */
    public function addText(string $text): void;

    /**
     * Adds a document to the store.
     *
     * @param  array  $texts  Identifier of the document (e.g., text or file path).
     *
     * @throws Exception
     */
    public function addTexts(array $texts): void;

    /**
     * Retrieves collections or documents pool.
     */
    public function getDocumentPool(): array;
}
