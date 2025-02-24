<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateQueuesMiddleware
{

    public function __construct(public int $userId)
    {

    }

    public function handle($job, Closure $next)
    {
          $user = User::find($this->userId);

        if ($user) {
            Auth::login($user);
        }

        $next($job);


    }
}
