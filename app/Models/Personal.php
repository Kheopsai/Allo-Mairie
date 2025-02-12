<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Personal extends Model
{


    protected $fillable = [
        'first_name',
        'last_name',
    ];

    public function model():MorphTo
    {
        return $this->morphTo(User::class,'model_type','model_id');
    }
}

