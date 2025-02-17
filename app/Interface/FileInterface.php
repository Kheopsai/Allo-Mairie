<?php

namespace App\Interface;

use Closure;

interface FileInterface
{
    public function exists(): bool;

    /**
     * @return resource|null
     */
    public function stream();

    public function store(mixed $contents, array $options = []): bool;

    public static function storing(Closure $callback): void;

    public static function stored(Closure $callback): void;
}
