<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasSource
{
    public function sources():MorphMany
    {
        return $this->morphMany();
    }
}
