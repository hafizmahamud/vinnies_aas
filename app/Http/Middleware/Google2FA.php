<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FALaravel\Middleware;
use PragmaRX\Google2FALaravel\Support\Authenticator;

class Google2FA extends Middleware
{
    /**
    * Handle an incoming request.
    *
    * @param  \Illuminate\Http\Request  $request
    */
    public function handle($request, Closure $next)
    {
        if (!Auth::user()->hasGoogle2FAEnabled()) {
            return $next($request);
        }

        $authenticator = app(Authenticator::class)->boot($request);

        if ($authenticator->isAuthenticated()) {
            return $next($request);
        }

        return $authenticator->makeRequestOneTimePasswordResponse();
    }
}
