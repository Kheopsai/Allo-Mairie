<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditRequest extends Model
{
    protected $fillable=['user_id','product_id','validated_at'];

    public function user():BelongsTo
    {
        return $this->belongsTo(SyncedUser::class);
    }

    public function product():BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

}
