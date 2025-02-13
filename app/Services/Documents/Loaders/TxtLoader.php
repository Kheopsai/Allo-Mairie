<?php

namespace App\Services\Documents\Loaders;

use App\Services\Documents\AbstractExtractor;
use RuntimeException;

class TxtLoader extends AbstractExtractor
{
    /**
     * @param  string  $input  The file path.
     * @param  string|null  $extension  The file extension, not used here.
     * @return array The file content as an array of lines.
     *
     * @throws RuntimeException If the file cannot be read.
     */
    public function extract($input, ?string $extension = null): array
    {
        if ($input === false) {
            throw new RuntimeException("Failed to read the file 'false'.");
        }

        return explode(PHP_EOL, $input);
    }

    public function countPages($input, ?string $extension = null): float
    {
        $linesPerPage = 40;

        if ($input === false) {
            throw new RuntimeException("Failed to read the file 'false'.");
        }

        $lines = substr_count($input, "\n");
        return ceil($lines / $linesPerPage);
    }
}
