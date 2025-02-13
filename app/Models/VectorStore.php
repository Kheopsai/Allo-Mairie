<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Pgvector\Laravel\HasNeighbors;
use Pgvector\Laravel\Vector;

class VectorStore extends Model
{
    use HasNeighbors;

    protected $casts= ['embedding'=> Vector::class];

    public function vectorable():MorphTo
    {
        return $this->morphTo();
    }

}
