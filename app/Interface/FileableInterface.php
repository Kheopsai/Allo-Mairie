<?php

namespace App\Interface;

use App\Services\FileSystem\FileAdder;
use Illuminate\Database\Eloquent\Relations\MorphMany;

interface FileableInterface
{
    public function files(): MorphMany;

    public function addFile($file): FileAdder;
}
