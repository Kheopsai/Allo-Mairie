<?php

namespace App\Services\Documents;

use Exception;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use thiagoalessio\TesseractOCR\TesseractOCR;
use thiagoalessio\TesseractOCR\TesseractOcrException;

abstract class AbstractExtractor
{
    const GLUE =".";
    /**
     * @throws Exception
     */
    protected function createTempStorage(): FilesystemAdapter
    {
        $uniquePath = Str::uuid7()->toString();
        $disk = Storage::build([
            'driver' => 'local',
            'root' => storage_path("app/{$uniquePath}"),
            'visibility' => 'public',
        ]);

        if (! $disk->exists('/')) {
            $disk->makeDirectory('/');
        }

        return $disk;
    }

    /**
     * @throws TesseractOcrException
     */
    protected function extractHtmlFromImages($imagePath): string
    {
        $ocr = new TesseractOCR($imagePath);

        return $ocr->run();
    }

    /**
     * @throws TesseractOcrException
     */
    protected function extractTextFromImages($imagePath): string
    {
        $ocr = new TesseractOCR($imagePath);

        return $ocr->run();
    }

    abstract public function extract($input, ?string $extension): array;
}
