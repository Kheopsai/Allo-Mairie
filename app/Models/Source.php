<?php

namespace App\Models;

use App\Traits\HasVectorStore;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Source extends Model implements HasMedia
{
    use HasVectorStore;
    use InteractsWithMedia;

    protected $fillable = ['name','type','content'];


    public function sourceable(): MorphTo
    {
       return $this->morphTo() ;
    }

}
