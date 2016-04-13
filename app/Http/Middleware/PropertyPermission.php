<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class PropertyPermission
{
	public function handle($request, Closure $next, $permission, $guard = null)
	{
		if ( Auth::guard($guard)->guest() )
		{
			abort(404);
		}

		if ( Auth::guard($guard)->user()->canProperty($permission, session('site_setup.site_id')) )
		{
			return $next($request);
		}

		abort(404);
	}
}
