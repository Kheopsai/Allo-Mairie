<?php

namespace App\Models;

use App\Traits\HasVectorStore;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Chat extends Model
{
    use HasVectorStore;

   protected $fillable=['sender','message','channel_id'] ;

   public function setMessageAttributes($value):void
   {
    $this->attributes['message']= sanitizeText($value);
   }

   public function channel():BelongsTo
   {
        return $this->belongsTo(Channel::class);
   }
}
