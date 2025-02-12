<?php

namespace App\Actions\Auth;

use Illuminate\Support\Facades\Auth;
use Lorisleiva\Actions\Concerns\AsAction;

class Logout
{
    use AsAction;

    public function handle()
    {
        Auth::logout();

        return redirect()->intended('/');
    }
}
