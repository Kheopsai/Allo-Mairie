<?php

namespace App\Services\Documents\Loaders;

use App\Enums\ExtensionEnum;
use App\Services\Documents\AbstractExtractor;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\Element\PageBreak;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Element\Title;
use PhpOffice\PhpWord\IOFactory;

class WordLoader extends AbstractExtractor
{
    /**
     * @throws Exception
     */
    public function extract($input, ?string $extension): array
    {
        $storage = $this->createTempStorage();
        $name = Str::uuid();
        $wordFilePath = Arr::join([$name, $extension], self::GLUE);
        if (! $storage->put($wordFilePath, $input)) {
            throw new Exception("Failed to store the Word file at {$storage->path($wordFilePath)}");
        }

        try {
            $phpWord = match ($extension) {
                ExtensionEnum::DOCX => IOFactory::load($storage->path($wordFilePath)),
                ExtensionEnum::DOC => IOFactory::load($storage->path($wordFilePath), 'MsDoc'),
                default => throw new Exception("Unsupported file type: {$extension}"),
            };
        } catch (\PhpOffice\PhpWord\Exception\Exception $e) {
            throw new Exception('Error loading the Word file: '.$e->getMessage().' at line '.$e->getLine());
        }
        try {
            $output = $this->readWordFileByPage($phpWord);
        } catch (Exception $e) {
            throw new Exception('Error processing the Word file: '.$e->getMessage());
        }

        $storage->delete($wordFilePath);

        return $output;
    }

    /**
     * @throws Exception
     */
    public function countPages($input, ?string $extension): int
    {
        return count($this->extract($input, $extension));

    }

    private function readWordFileByPage($phpWord): array
    {
        $pages = [];
        $currentPageText = '';

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if ($element === null) {
                    continue;
                }

                $elementText = $this->getTextFromElement($element, $isNewPage);
                if ($isNewPage && $currentPageText !== '') {
                    $pages[] = $currentPageText;
                    $currentPageText = '';
                }
                $currentPageText .= $elementText;
            }
        }

        if (! empty($currentPageText)) {
            $pages[] = $currentPageText;
        }

        return $pages;
    }

    private function getTextFromElement($element, &$isNewPage): string
    {
        $text = '';
        $isNewPage = false;

        if ($element === null) {
            return $text;
        }

        if ($element instanceof TextRun) {
            foreach ($element->getElements() as $subElement) {
                if ($subElement === null) {
                    continue;
                }
                $text .= $this->getTextFromElement($subElement, $isNewPage);
                if ($isNewPage) {
                    break;
                }
            }
        } elseif (method_exists($element, 'getText')) {
            $result = $element->getText();
            if (is_string($result)) {
                $text .= $result;
            } elseif (is_object($result) && method_exists($result, '__toString')) {
                $text .= $result;
            }
        } elseif ($element instanceof Title) {
            $text .= $element->getText();
            $text .= "\n";
        } elseif ($element instanceof PageBreak) {
            $isNewPage = true;
        }

        return $text;
    }
}
