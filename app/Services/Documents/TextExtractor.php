<?php

namespace App\Services\Documents;


use App\Enums\ExtensionEnum;
use App\Services\Documents\Loaders\ExcelLoader;
use App\Services\Documents\Loaders\PdfLoader;
use App\Services\Documents\Loaders\PptLoader;
use App\Services\Documents\Loaders\TxtLoader;
use App\Services\Documents\Loaders\WordLoader;
use Exception;
use finfo;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;
use Spatie\PdfToImage\Exceptions\PdfDoesNotExist;

class TextExtractor
{
    /**
     * @throws Exception
     */
    public function extractText(string $input): bool|string|array
    {
        [$content, $extension] = $this->determineContent($input);

        return match ($extension) {
            ExtensionEnum::TXT => (new TxtLoader)->extract($content, $extension),
            ExtensionEnum::PDF => (new PdfLoader)->extract($content, $extension),
            ExtensionEnum::PPT, ExtensionEnum::PPTX => (new PptLoader)->extract($content, $extension),
            ExtensionEnum::DOC, ExtensionEnum::DOCX => (new WordLoader)->extract($content, $extension),
            ExtensionEnum::XLS, ExtensionEnum::XLSX => (new ExcelLoader)->extract($content, $extension),
            default => throw new Exception("Unsupported file type: {$extension}"),
        };
    }

    /**
     * @throws PdfDoesNotExist
     * @throws FileNotFoundException
     * @throws Exception
     */
    public function countPages(string $input): bool|string|array
    {
        [$content, $extension] = $this->determineContent($input);

        return match ($extension) {
            ExtensionEnum::TXT => (new TxtLoader)->countPages($content),
            ExtensionEnum::PDF => (new PdfLoader)->countPages($content),
            ExtensionEnum::PPT, ExtensionEnum::PPTX => (new PptLoader)->countPages($content, $extension),
            ExtensionEnum::DOC, ExtensionEnum::DOCX => (new WordLoader)->countPages($content, $extension),
            ExtensionEnum::XLS, ExtensionEnum::XLSX => (new ExcelLoader)->countPages($content, $extension),
            default => throw new Exception("Unsupported file type: {$extension}"),
        };
    }

    /**
     * @throws FileNotFoundException
     */
    private function determineContent(mixed $input): array
    {
        $content = '';
        $extension = ExtensionEnum::UNKNOWN;

        if (File::exists($input)) {
            $extension = File::extension($input);
            $content = File::get($input);
        } elseif (is_file($input)) {
            $extension = File::extension($input->getRealPath());
            $content = File::get($input->getRealPath());
        } elseif (is_string($input)) {
            $info = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $info->buffer($input);
            $content = $input;
            $extension = $this->mapMimeTypeToExtension($mimeType);
        }

        return match ($extension) {
            ExtensionEnum::PDF => [$input, $extension],
            default => [$content, $extension]
        };
    }

    private function mapMimeTypeToExtension(string $mimeType): string
    {
        return match ($mimeType) {
            'application/pdf' => ExtensionEnum::PDF,
            'text/plain' => ExtensionEnum::TXT,
            'application/msword' => ExtensionEnum::DOC,
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => ExtensionEnum::DOCX,
            'application/vnd.ms-excel' => ExtensionEnum::XLS,
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => ExtensionEnum::XLSX,
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => ExtensionEnum::PPTX,
            default => ExtensionEnum::UNKNOWN,
        };
    }
}
