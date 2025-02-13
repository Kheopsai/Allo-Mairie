<?php

namespace App\Services\Documents\Loaders;

use App\Enums\ExtensionEnum;
use App\Services\Documents\AbstractExtractor;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PhpOffice\PhpPresentation\IOFactory as PresentationIOFactory;
use PhpOffice\PhpPresentation\Shape\RichText;

class PptLoader extends AbstractExtractor
{
    const string GLUE = '.';

    /**
     * @throws Exception
     */
    public function extract($input, $extension): array
    {
        $storage = $this->createTempStorage();
        $name = Str::uuid();
        $path = Arr::join([$name, $extension], self::GLUE);
        if (! $storage->put($path, $input)) {
            throw new Exception("Failed to store the PowerPoint file at {$storage->path($path)}");
        }
        $texts = [];
        try {
            $pptReader = match ($extension) {
                ExtensionEnum::PPT => PresentationIOFactory::createReader('PowerPoint97'),
                ExtensionEnum::PPTX => PresentationIOFactory::createReader('PowerPoint2007'),
                default => throw new Exception('Invalid file type'),
            };
            $oPHPPresentation = $pptReader->load($storage->path($path));
            foreach ($oPHPPresentation->getAllSlides() as $slide) {
                $slideTexts = [];
                foreach ($slide->getShapeCollection() as $shape) {
                    if ($shape instanceof RichText) {
                        foreach ($shape->getParagraphs() as $paragraph) {
                            foreach ($paragraph->getRichTextElements() as $element) {
                                $text = $element->getText();
                                $slideTexts[] = $text;
                            }
                        }
                    }
                }
                $texts[] = implode("\n", $slideTexts);
            }
        } catch (Exception $e) {
        }
        $storage->delete($path);

        return $texts;
    }

    /**
     * @throws Exception
     */
    public function countPages($input, $extension): int
    {
        $storage = $this->createTempStorage();
        $name = Str::uuid();

        $path = Arr::join([$name, $extension], self::GLUE);

        if (! $storage->put($path, $input)) {
            throw new Exception("Failed to store the PowerPoint file at {$storage->path($path)}");
        }

        try {

            $pptReader = match ($extension) {
                ExtensionEnum::PPT => PresentationIOFactory::createReader('PowerPoint97'),
                ExtensionEnum::PPTX => PresentationIOFactory::createReader('PowerPoint2007'),
                default => throw new Exception('Invalid file type'),
            };
            $oPHPPresentation = $pptReader->load($storage->path($path));
            $storage->delete($path);

            return $oPHPPresentation->getSlideCount();
        } catch (Exception $e) {
            Log::error("Error during PPT extraction: {$e->getMessage()}");
            throw $e;
        }
    }
}
