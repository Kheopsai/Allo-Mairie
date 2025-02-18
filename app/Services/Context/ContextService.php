<?php

namespace App\Services\Context;

use App\Interface\VectorStorageInterface;
use App\Services\Embeddings\Embedding;
use Exception;
use Illuminate\Support\Arr;
use Lorisleiva\Actions\Concerns\AsAction;

class ContextService
{
    use AsAction;

    private ?VectorStorageInterface $storage;

    private ?string $prompt;

    private int|array|null $collection;

    private array $result = [];

    private string $content = '';

    public function __construct(?string $prompt = null, int|array|null $collection = null, ?VectorStorageInterface $vectorStorage = null)
    {
        $this->storage = $vectorStorage;
        $this->prompt = $prompt;
        $this->collection = $collection;
    }

    /**
     * @throws Exception
     */
    public function search(): self
    {
        if ($this->prompt === null) {
            throw new Exception('Prompt cannot be null');
        }
        if (is_array($this->collection)) {
            $this->result = $this->collection;
        } else {
            $embedding = Embedding::handle($this->prompt);
            $this->result = $this->storage->searchWithEmbedding($embedding, $this->collection);
        }

        return $this;
    }

    /**
     * Reranks the search results using TF-IDF and cosine similarity.
     */
    public function rerank(): self
    {
        $promptTokens = $this->tokenize($this->prompt);
        $promptTf = $this->termFrequencies($promptTokens);

        $idf = $this->inverseDocumentFrequencies($this->result);

        $promptTfIdf = [];
        foreach ($promptTf as $token => $tf) {
            $promptTfIdf[$token] = $tf * ($idf[$token] ?? 0);
        }

        $documentScores = [];

        foreach ($this->result as $index => $document) {
            $documentTokens = $this->tokenize($document['text']);
            $documentTf = $this->termFrequencies($documentTokens);

            $documentTfIdf = [];
            foreach ($documentTf as $token => $tf) {
                $documentTfIdf[$token] = $tf * ($idf[$token] ?? 0);
            }

            $score = $this->cosineSimilarity($promptTfIdf, $documentTfIdf);

            $documentScores[$index] = $score;
        }

        foreach ($this->result as $index => &$document) {
            $document['score'] = $documentScores[$index];
        }
        unset($document);
        usort($this->result, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return $this;
    }

    public function pluck($value): self
    {
        $this->result = collect($this->result)->pluck($value)->toArray();

        return $this;
    }

    public function merge(): self
    {
        $count = collect($this->result)->count();
        if ($count !== 0) {
            $collection = collect($this->result)->pluck('text');
            $this->content = Arr::join($collection->toArray(), '\n');
        } else {
            $this->content = '';
        }

        return $this;
    }

    public function send(): string
    {
        return $this->content;
    }

    /**
     * @param  int  $maxTokens  The maximum number of tokens allowed.
     * @return string The concatenated context string suitable for LLM consumption.
     *
     * @throws Exception
     */
    public function concatenateContextsWithLimit(int $maxTokens = 8192): string
    {
        $concatenatedContext = '';
        $currentTokenCount = 0;

        foreach ($this->result as $index => $context) {
            $contextText = $this->cleanString($context['text'] ?? $context);

            $contextTokenCount = $this->estimateTokenCount($contextText);

            if ($currentTokenCount + $contextTokenCount > $maxTokens) {
                break;
            }

            $concatenatedContext .= '# document '.($index + 1).":\n";
            $concatenatedContext .= $contextText."\n\n";

            $currentTokenCount += $contextTokenCount;
        }

        return trim($concatenatedContext);
    }

    /**
     * @param  string  $text  The text to estimate tokens for.
     * @return int The estimated token count.
     */
    public function estimateTokenCount(string $text): int
    {
        $textWithoutSpaces = preg_replace('/\s+/', '', $text);

        $characters = strlen($textWithoutSpaces);

        $averageCharsPerToken = 4;

        return (int) ceil($characters / $averageCharsPerToken);
    }

    /**
     * @param  string  $context  The string to be cleaned.
     * @return string The cleaned string with consistent spacing.
     */
    public function cleanString(string $context): string
    {
        // Remove unnecessary whitespace and control characters
        $context = preg_replace('/[ \t]+/', ' ', $context);
        $context = preg_replace('/[\r\n]+/', "\n", $context);

        return trim($context);
    }

    /**
     * Tokenizes the input text into an array of words.
     */
    private function tokenize(string $text): array
    {
        // Convert text to lowercase
        $text = strtolower($text);

        // Remove punctuation
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', '', $text);

        // Split into words
        return preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * Computes term frequencies for a set of tokens.
     */
    private function termFrequencies(array $tokens): array
    {
        $tf = [];
        $count = count($tokens);

        foreach ($tokens as $token) {
            if (! isset($tf[$token])) {
                $tf[$token] = 0;
            }
            $tf[$token] += 1;
        }

        // Normalize by total token count
        foreach ($tf as $token => $freq) {
            $tf[$token] = $freq / $count;
        }

        return $tf;
    }

    /**
     * Computes inverse document frequencies for the result set.
     */
    private function inverseDocumentFrequencies(array $documents): array
    {
        $idf = [];
        $N = count($documents);

        // Document frequencies
        $df = [];

        foreach ($documents as $document) {
            $tokens = $this->tokenize($document['text']);
            $uniqueTokens = array_unique($tokens);

            foreach ($uniqueTokens as $token) {
                if (! isset($df[$token])) {
                    $df[$token] = 0;
                }
                $df[$token] += 1;
            }
        }

        // Compute IDF
        foreach ($df as $token => $freq) {
            $idf[$token] = log($N / ($freq));
        }

        return $idf;
    }

    /**
     * Computes cosine similarity between two vectors.
     */
    private function cosineSimilarity(array $vec1, array $vec2): float
    {
        $dotProduct = 0;
        $normVec1 = 0;
        $normVec2 = 0;

        $allTokens = array_unique(array_merge(array_keys($vec1), array_keys($vec2)));

        foreach ($allTokens as $token) {
            $v1 = $vec1[$token] ?? 0;
            $v2 = $vec2[$token] ?? 0;

            $dotProduct += $v1 * $v2;
            $normVec1 += $v1 * $v1;
            $normVec2 += $v2 * $v2;
        }

        if ($normVec1 == 0 || $normVec2 == 0) {
            return 0.0;
        }

        return $dotProduct / (sqrt($normVec1) * sqrt($normVec2));
    }

        public function getSelectedDocuments(int $maxTokens = 8192, bool $filterNullVectorable = true): array
    {
        $selectedDocuments = [];
        $currentTokenCount = 0;

        $documents = $filterNullVectorable
            ? collect($this->result)->whereNull('vectorable_id')->whereNull('vectorable_type')->toArray()
            : $this->result;

        foreach ($documents as $context) {
            $contextText = $this->cleanString($context['text'] ?? $context);
            $contextTokenCount = $this->estimateTokenCount($contextText);

            if ($currentTokenCount + $contextTokenCount > $maxTokens) {
                break;
            }

            $selectedDocuments[] = $context;
            $currentTokenCount += $contextTokenCount;
        }

        return $selectedDocuments;
    }
}
