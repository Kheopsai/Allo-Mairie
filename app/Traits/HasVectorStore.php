<?php

namespace App\Traits;

use App\Models\VectorStore;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasVectorStore
{
    public function vectorStores():MorphMany
    {
        return $this->morphMany(VectorStore::class,'vectorable');
    }
}
