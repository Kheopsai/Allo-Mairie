<?php

namespace App\Models;

use App\Traits\HasFiles;
use App\Traits\HasVectorStore;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Tags\HasTags;

/**
 * @property mixed $id
 * @property mixed $type
 * @property int|mixed|string|null $user_id
 * @property mixed $content
 * @property mixed $name
 */
class Source extends Model implements HasMedia
{
    use HasVectorStore;
    use InteractsWithMedia;
    use HasTags;
    use HasFiles;

    protected $fillable = ['name','type','content','user_id','hub_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(SyncedUser::class,'user_id');
    }

    public function sourceable(): MorphTo
    {
       return $this->morphTo() ;
    }

    public function hub():BelongsTo
    {
        return $this->belongsTo(Hub::class);
    }

}
