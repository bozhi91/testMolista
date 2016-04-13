<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AuthenticateAccount
{
	/**
	* Handle an incoming request.
	*
	* @param  \Illuminate\Http\Request  $request
	* @param  \Closure  $next
	* @param  string|null  $guard
	* @return mixed
	*/
	public function handle($request, Closure $next, $guard = null)
	{
		if ( Auth::guard($guard)->guest() || !Auth::user()->hasRole(['company','employee']) ) 
		{
			if ( $request->ajax() || $request->wantsJson() ) 
			{
				return response('Unauthorized.', 401);
			} 
			else 
			{
				if ( Auth::guard($guard)->guest() )
				{
					return redirect()->guest( action('Auth\AuthController@showLoginForm') );
				}

				return redirect()->guest( action('WebController@index') );
			}
		}

		// Check if site is allowed
		Auth::user()->sites()->findOrFail( session()->get('site_setup.site_id') );

		return $next($request);
	}
}