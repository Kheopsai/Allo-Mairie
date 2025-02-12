<?php

namespace App\Generators;

use Illuminate\Support\Str;
use Stancl\Tenancy\Contracts\UniqueIdentifierGenerator;

class CustomTenantIdGenerator implements UniqueIdentifierGenerator
{
    public static function generate($resource): string
    {
        return Str::uuid7()->toString();
    }
}
