<?php

namespace App\Observers;

use App\Models\Credit;
use App\Models\CreditTransactions;
use App\Models\SyncedUser;

class SyncedUserObserver
{
    /**
     * Handle the SyncedUser "created" event.
     */
    public function created(SyncedUser $user): void
    {
        $baseCredits = config('llm.credits');
        $credits = Credit::create([
            'user_id' => $user->id,
            'total_credits' => $baseCredits,
            'available_credits' => $baseCredits,
        ]);
        CreditTransactions::create([
            'credit_id' => $credits->id,
            'amount' => $baseCredits,
        ]);
    }

    /**
     * Handle the SyncedUser "updated" event.
     */
    public function updated(SyncedUser $syncedUser): void
    {
        //
    }

    /**
     * Handle the SyncedUser "deleted" event.
     */
    public function deleted(SyncedUser $syncedUser): void
    {
        //
    }

    /**
     * Handle the SyncedUser "restored" event.
     */
    public function restored(SyncedUser $syncedUser): void
    {
        //
    }

    /**
     * Handle the SyncedUser "force deleted" event.
     */
    public function forceDeleted(SyncedUser $syncedUser): void
    {
        //
    }
}
