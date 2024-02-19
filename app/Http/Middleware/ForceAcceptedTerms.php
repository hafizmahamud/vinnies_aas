<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class ForceAcceptedTerms
{
    /**
    * Handle an incoming request.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \Closure  $next
    * @return mixed
    */
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            return $next($request);
        }

        if ($request->isMethod('post')) {
            return $next($request);
        }

        if (Route::currentRouteName() !== 'home.tos' && !$request->user()->has_accepted_terms) {
            return redirect()->route('home.tos');
        }

        return $next($request);
    }
}
