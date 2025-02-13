<?php

namespace App\Services\Documents\Loaders;

use App\Enums\ExtensionEnum;
use App\Services\Documents\AbstractExtractor;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\Async\Pool;
use Spatie\PdfToImage\Exceptions\PdfDoesNotExist;
use Spatie\PdfToImage\Pdf;

class PdfLoader extends AbstractExtractor
{
    /**
     * @throws Exception
     */
    private function cleanAndStorePdf(string $path): string
    {
        $storage = $this->createTempStorage();
        $pdfContent = file_get_contents($path);
        $startPos = strpos($pdfContent, '%PDF-');

        if ($startPos === false) {
            Log::warning("No '%PDF-' header found in file {$path}");

            return $path;
        }
        $cleanContent = substr($pdfContent, $startPos);
        $filename = time().'.pdf';

        $storage->put($filename, $cleanContent);

        return $storage->path($filename);
    }

    /**
     * @throws PdfDoesNotExist
     * @throws Exception
     */
    public function extract($input, $extension): array
    {
        $cleanedPdfPath = $this->cleanAndStorePdf($input);
        try {
            $storage = $this->createTempStorage();
            $pdf = new Pdf($cleanedPdfPath);
            $pool = Pool::create();
            $texts = [];
            for ($i = 1; $i <= $pdf->getNumberOfPages(); $i++) {
                $image = Str::uuid()->toString().'.'.ExtensionEnum::PNG;
                $imagePath = $storage->path($image);

                $pool->add(function () use ($i, $pdf, $imagePath) {
                    $pdf->setPage($i)->saveImage($imagePath);
                })->then(function () use ($imagePath, &$texts) {
                    $text = $this->extractTextFromImages($imagePath);
                    unlink($imagePath);
                    $texts[] = $text;
                })->catch(function (Exception $exception) {
                    Log::error("Error processing page image: {$exception->getMessage()}");
                });
            }

            $pool->wait();
            $storage->deleteDirectory('/');

            return $texts;
        } catch (Exception $e) {
            Log::error("Error during PDF extraction: {$e->getMessage()}");
            throw $e;
        } finally {
            unlink($cleanedPdfPath);
        }
    }

    /**
     * @throws PdfDoesNotExist
     * @throws Exception
     */
    public function countPages($input): int
    {
        $cleanedPdfPath = $this->cleanAndStorePdf($input);
        try {
            $pdf = new Pdf($cleanedPdfPath);
            return $pdf->getNumberOfPages();
        } catch (Exception $e) {
            Log::error("Error during PDF extraction: {$e->getLine()}");
            throw $e;
        } finally {
            unlink($cleanedPdfPath);
        }
    }
}
