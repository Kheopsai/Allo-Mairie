<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Credit extends Model
{
    protected $fillable = ['user_id', 'total_credits', 'available_credits', 'monthly_credit', 'permanent_credit'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(CreditTransactions::class);
    }

    public static function boot()
    {
        parent::boot();

        // Update total when column_a or column_b changes
        static::saving(function ($model) {
            $model->available_credits = $model->monthly_credit + $model->permanent_credit;
        });

        // When total is updated manually, adjust column_a and column_b
        static::updating(function ($model) {
            ds('subtracting');
            if ($model->isDirty('available_credits')) {
                $difference = $model->available_credits - $model->getOriginal('available_credits');

                ds($difference);
                if ($difference < 0) { // Subtraction case
                    // Prioritize subtraction from column_b, then column_a
                    $absDiff = abs($difference);

                    if ($model->monthly_credit >= $absDiff) {
                        $model->monthly_credit -= $absDiff;
                    } else {
                        $remaining = $absDiff - $model->monthly_credit;
                        $model->monthly_credit = 0;
                        $model->permanent_credit -= $remaining;
                    }
                }
            }
        });
    }
}
