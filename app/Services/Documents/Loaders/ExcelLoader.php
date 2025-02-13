<?php

namespace App\Services\Documents\Loaders;

use App\Enums\ExtensionEnum;
use App\Services\Documents\AbstractExtractor;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class ExcelLoader extends AbstractExtractor
{
    /**
     * @throws Exception
     */
    public function extract($input, ?string $extension): array
    {
        $storage = $this->createTempStorage();
        $name = Str::uuid();
        $path = Arr::join([$name, $extension], self::GLUE);
        $storage->put($path, $input);

        $reader = match ($extension) {
            ExtensionEnum::XLS => new Xls,
            ExtensionEnum::XLSX => new Xlsx,
            default => throw new Exception('Invalid file type'),
        };
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($storage->path($path));
        $storage->delete($path);
        $sheet = $spreadsheet->getSheet($spreadsheet->getFirstSheetIndex());
        return array_map(fn ($row) => implode(',', $row), $sheet->toArray());
    }

    /**
     * @throws Exception
     */
    public function countPages($input, ?string $extension): int
    {

        $storage = $this->createTempStorage();
        $name = Str::uuid();
        $path = Arr::join([$name, $extension], self::GLUE);
        $storage->put($path, $input);

        $reader = match ($extension) {
            ExtensionEnum::XLS => new Xls,
            ExtensionEnum::XLSX => new Xlsx,
            default => throw new Exception('Invalid file type'),
        };
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($storage->path($path));
        $storage->delete($path);

        return $spreadsheet->getSheetCount();
    }
}
