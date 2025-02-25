<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditTransactions extends Model
{
    protected $fillable = ['credit_id', 'amount', 'description'];

    public function credit(): BelongsTo
    {
        return $this->belongsTo(Credit::class);
    }
}
