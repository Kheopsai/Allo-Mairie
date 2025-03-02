<?php

namespace App\Services\Tokens;

use App\Enums\CreditEnum;
use App\Interface\LlmServiceProviderInterface;
use App\Models\CreditTransactions;
use App\Models\SyncedUser;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class CreditService
{
    /**
     * @throws Throwable
     */
    public function consumeCredits(User|SyncedUser $user, LlmServiceProviderInterface $model, int $tokens)
    {
        $creditsToDeduct = ceil($tokens / $model->conversion_rate);

        return DB::transaction(function () use ($user, $creditsToDeduct) {
            if ($user->credits->available_credits < $creditsToDeduct) {
                throw new Exception(trans('Insufficient credits'));
            }

            $user->credits->available_credits -= $creditsToDeduct;
            $user->credits->save();

            CreditTransactions::create([
                'credit_id' => $user->credits->id,
                'amount' => -$creditsToDeduct,
            ]);

            return $creditsToDeduct;
        });
    }

    /**
     * @throws Throwable
     */
    public function addCredits(User|SyncedUser $user, int $amount,$creditType= CreditEnum::Permanent)
    {
        return DB::transaction(function () use ($user, $amount,$creditType) {
            switch($creditType){
                case CreditEnum::Permanent:
                    $user->credits->permanent_credit += $amount;
                break;
                case CreditEnum::Monthly:
                    $user->credits->monthly_credit = $amount;
                break;
            }
            $user->credits->total_credits += $amount;
            $user->credits->save();

            CreditTransactions::create([
                'credit_id' => $user->credits->id,
                'amount' => $amount,
            ]);

            return $amount;
        });
    }
}
