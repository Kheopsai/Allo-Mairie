<?php

namespace App\Models;

use App\Traits\HasFiles;
use App\Traits\HasVectorStore;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Tags\HasTags;

class Source extends Model implements HasMedia
{
    use HasVectorStore;
    use InteractsWithMedia;
    use HasTags;
    use HasFiles;

    protected $fillable = ['name','type','content','user_id'];


    public function sourceable(): MorphTo
    {
       return $this->morphTo() ;
    }

}
