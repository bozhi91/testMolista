<?php

namespace App\Http\Middleware;

use Closure;

class SiteLoginRoles
{
	public function handle($request, Closure $next, $roles)
	{
		// Set login required roles
		putenv( "loginRequiredRoles=" . $roles );

		return $next($request);
	}
}
