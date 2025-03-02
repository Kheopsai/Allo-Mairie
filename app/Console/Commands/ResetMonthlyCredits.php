<?php

namespace App\Console\Commands;

use App\Models\SyncedUser;
use App\Services\Tokens\CreditService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ResetMonthlyCredits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-monthly-credits';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $creditsService= new CreditService;
        SyncedUser::with('credits')->all()->each(function ($user) use ($creditsService){

            $lastReset= $user->credits->update_at ?? $user->created_at;
            if(Carbon::parse($lastReset)->addMonth() <= now()){
                $creditsService->addCredits($user,config('llm.credits'));
            }
        });
    }
}
