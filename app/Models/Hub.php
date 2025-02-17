<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class Hub extends Model
{

    use HasRelationships;

    protected $fillable= ['name','description','user_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(SyncedUser::class,'user_id');

    }

    public function sources():HasMany
    {
        return $this->hasMany(Source::class);
    }

    public function vectorStores(): HasManyDeep
    {
        return $this->hasManyDeep(VectorStore::class,[Source::class],[null,'vectorable_type','vectorable_id']);
    }

}
