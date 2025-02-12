<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Chat extends Model
{
   protected $fillable=['sender','message','channel_id'] ;

   public function channel():BelongsTo
   {
        return $this->belongsTo(Channel::class);
   }
}
