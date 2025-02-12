<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Channel extends Model
{
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

    public function chats():HasMany
    {
        return $this->hasMany(Chat::class);
    }
}
