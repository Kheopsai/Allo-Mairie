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

            if ($model->isDirty('available_credits')) {
                $originalCredits = $model->getOriginal('available_credits') ?? 0;
                $newCredits = $model->available_credits;
                $difference = $newCredits - $originalCredits;

                if ($difference < 0) { // Only adjust when subtracting credits
                    $absDiff = abs($difference);

                    // Deduct from monthly_credit first, then permanent_credit
                    if ($model->monthly_credit >= $absDiff) {
                        $model->monthly_credit -= $absDiff;
                    } else {
                        $remaining = $absDiff - $model->monthly_credit;
                        $model->monthly_credit = 0;

                        if ($model->permanent_credit >= $remaining) {
                            $model->permanent_credit -= $remaining;
                        }
                    }

                    // Ensure available_credits remains consistent
                }
            }
            $model->available_credits = $model->monthly_credit + $model->permanent_credit;
            // $model->available_credits = $model->monthly_credit + $model->permanent_credit;
        });

        // When total is updated manually, adjust column_a and column_b
        // static::updating(function ($model) {
        //     if ($model->isDirty('available_credits')) {
        //         $originalCredits = $model->getOriginal('available_credits') ?? 0;
        //         $newCredits = $model->available_credits;
        //         $difference = $newCredits - $originalCredits;

        //         if ($difference < 0) { // Only adjust when subtracting credits
        //             $absDiff = abs($difference);

        //             // Deduct from monthly_credit first, then permanent_credit
        //             if ($model->monthly_credit >= $absDiff) {
        //                 $model->monthly_credit -= $absDiff;
        //             } else {
        //                 $remaining = $absDiff - $model->monthly_credit;
        //                 $model->monthly_credit = 0;

        //                 if ($model->permanent_credit >= $remaining) {
        //                     $model->permanent_credit -= $remaining;
        //                 } else {
        //                     throw new \Exception('Not enough credits to deduct.'); // Prevents negative values
        //                 }
        //             }

        //             // Ensure available_credits remains consistent
        //             $model->available_credits = $model->monthly_credit + $model->permanent_credit;
        //         }
        //     }
        // });
    }
}
