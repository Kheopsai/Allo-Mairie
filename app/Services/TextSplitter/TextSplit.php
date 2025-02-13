<?php

namespace App\Services\TextSplitter;

use App\Services\Documents\TextExtractor;
use Exception;
use Generator;
use Illuminate\Support\Arr;
use Lorisleiva\Actions\Concerns\AsAction;
use SplFileInfo;

class TextSplit
{
    use AsAction;

    protected int $chunkSize;

    protected int $chunkOverlap;

    private TextExtractor $textExtractor;

    private array $abbreviations;

    private array $identifierPatterns;

    public function __construct(
        int $chunkSize = 1000,
        int $chunkOverlap = 1,
        array $abbreviations = ['Mr.', 'Mrs.', 'Dr.', 'Prof.', 'Inc.', 'Ltd.', 'Jr.', 'Sr.', 'vs.', 'e.g.', 'i.e.'],
        array $identifierPatterns = ['/\b\d+\.\w+\b/'],
        ?TextExtractor $textExtractor = null
    ) {
        $this->chunkSize = $chunkSize;
        $this->chunkOverlap = $chunkOverlap;
        $this->abbreviations = $abbreviations;
        $this->identifierPatterns = $identifierPatterns;
        $this->textExtractor = $textExtractor ?? new TextExtractor;
    }

    /**
     * @param  string  $text  The input text to be split.
     * @return array An array of text chunks.
     */
    public function handle(string $text): array
    {
        $cleanedText = $this->cleanText($text);
        $sentences = $this->splitIntoSentences($cleanedText);

        return $this->assembleChunks($sentences);
    }

    /**
     * @param  string|SplFileInfo  $filePath  The path to the file or a SplFileInfo instance.
     * @return Generator Yields text chunks one by one.
     * @throws Exception If text extraction fails.
     */
    public function fromFile(string|SplFileInfo $filePath): Generator
    {
        $extractedText = $this->extractText($filePath);
        $cleanedText = $this->cleanText(Arr::join($extractedText, ' '));
        $sentences = $this->splitIntoSentences($cleanedText);

        foreach ($this->assembleChunks($sentences) as $chunk) {
            yield $chunk;
        }
    }

    /**
     * @param  string|SplFileInfo  $filePath  The path to the file or a SplFileInfo instance.
     * @return array An array of text chunks.
     *
     * @throws Exception If text extraction fails.
     */
    public function fromFileToArray(string|SplFileInfo $filePath): array
    {
        $extractedText = $this->extractText($filePath);
        $cleanedText = $this->cleanText(Arr::join($extractedText, ' '));
        $sentences = $this->splitIntoSentences($cleanedText);

        return $this->assembleChunks($sentences);
    }

    /**
     * @param  string  $text  The cleaned text to split.
     * @return array An array of sentences.
     */
    private function splitIntoSentences(string $text): array
    {
        foreach ($this->abbreviations as $abbr) {
            $escapedAbbr = str_replace('.', '<ABBR_DOT>', $abbr);
            $text = str_replace($abbr, $escapedAbbr, $text);
        }

        $pattern = '/(?<=[.!?])\s+(?=[A-Z])/';
        $sentences = preg_split($pattern, $text, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($this->abbreviations as $abbr) {
            $escapedAbbr = str_replace('.', '<ABBR_DOT>', $abbr);
            $text = str_replace($escapedAbbr, $abbr, $text);
        }

        return $sentences;
    }

    /**
     * @param  array  $sentences  The array of sentences to assemble.
     * @return array An array of text chunks.
     */
    private function assembleChunks(array $sentences): array
    {
        $chunks = [];
        $currentChunk = '';
        $currentLength = 0;
        $overlapBuffer = [];

        foreach ($sentences as $sentence) {
            $sentenceLength = mb_strlen($sentence) + 1;

            if ($currentLength + $sentenceLength > $this->chunkSize) {
                if ($currentChunk !== '') {
                    $chunks[] = trim($currentChunk);

                    if ($this->chunkOverlap > 0) {
                        $overlapBuffer = array_slice(explode(' ', $currentChunk), -$this->chunkOverlap);
                        $currentChunk = implode(' ', $overlapBuffer).' ';
                        $currentLength = mb_strlen($currentChunk);
                    } else {
                        $currentChunk = '';
                        $currentLength = 0;
                    }
                }
            }

            $currentChunk .= $sentence.' ';
            $currentLength += $sentenceLength;
        }

        if (trim($currentChunk) !== '') {
            $chunks[] = trim($currentChunk);
        }

        return $chunks;
    }

    /**
     * @param  string  $text  The text to clean.
     * @return string The cleaned text.
     */
    public function cleanText(string $text): string
    {
        return preg_replace('/\s+/', ' ', trim($text));
    }

    /**
     * @param  string|SplFileInfo  $filePath  The path to the file or a SplFileInfo instance.
     * @return array The extracted text as an array of lines or paragraphs.
     *
     * @throws Exception If text extraction fails.
     */
    private function extractText(string|SplFileInfo $filePath): array
    {
        if ($filePath instanceof SplFileInfo) {
            $path = $filePath->getRealPath();
            if ($path === false) {
                throw new Exception('Invalid file path provided.');
            }

            return $this->textExtractor->extractText($path);
        }

        return $this->textExtractor->extractText($filePath);
    }

    /**
     * @param  array  $abbreviations  Array of abbreviations.
     */
    public function setAbbreviations(array $abbreviations): self
    {
        $this->abbreviations = $abbreviations;

        return $this;
    }

    /**
     * @param  array  $identifierPatterns  Array of regex patterns for identifiers.
     */
    public function setIdentifierPatterns(array $identifierPatterns): self
    {
        $this->identifierPatterns = $identifierPatterns;

        return $this;
    }

    /**
     * @param  int  $chunkSize  Size of each text chunk in characters.
     */
    public function setChunkSize(int $chunkSize): self
    {
        $this->chunkSize = $chunkSize;

        return $this;
    }

    /**
     * @param  int  $chunkOverlap  Number of sentences to overlap between chunks.
     */
    public function setChunkOverlap(int $chunkOverlap): self
    {
        $this->chunkOverlap = $chunkOverlap;

        return $this;
    }
}
