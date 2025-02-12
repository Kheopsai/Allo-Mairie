<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;

trait HasDomain
{
    protected function ProcessedName(): Attribute
    {
        return Attribute::make(
            get: function () {
                $domain = $this->attributes['domain'];
                $commonExtensions = config('domains.extensions');
                $hasCommonExtension = collect($commonExtensions)->contains(function ($extension) use ($domain) {
                    return Str::endsWith($domain, $extension);
                });

                if (! $hasCommonExtension) {
                    $subdomainPrefix = config('tenancy.domain');
                    $domain = "{$domain}.{$subdomainPrefix}";
                }

                return $domain;
            },
        );
    }
}
