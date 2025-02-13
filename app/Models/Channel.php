<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class Channel extends Model
{
    use HasRelationships;

    public $incrementing = false;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'user_id',
    ];

    public function user():BelongsTo
    {
        return $this->belongsTo(SyncedUser::class);
    }

    public function vectorStores():HasManyDeep
    {
        return $this->hasManyDeep(VectorStore::class,[Chat::class],[null,'vectorable_type','vectorable_id']);
    }

    public function chats():HasMany
    {
        return $this->hasMany(Chat::class);
    }
}
