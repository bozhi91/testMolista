<?php

namespace App\Http\Middleware;

use Closure;

class AuthenticateApi
{
    /**
    * Handle an incoming request.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \Closure  $next
    * @param  string|null  $guard
    * @return mixed
    */
    public function handle($request, Closure $next)
    {

        // Validate key
        $key = \App\Models\User\ApiKey::getCurrent();
        if ( $key && $key->user->enabled )
        {
            return $next($request);
        }

        return response('Unauthorized.', 401);
    }
}