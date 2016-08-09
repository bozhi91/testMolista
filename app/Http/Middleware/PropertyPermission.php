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

		// Get site ID
		$site_id = \App\Session\Site::get('site_id', false);

		// Can publish in this site
		if ( !Auth::guard($guard)->user()->canProperty($permission, $site_id) )
		{
			abort(404);
		}

		return $next($request);

		// Check max allowed
		/*
		$properties_allowed = @intval( \App\Session\Site::get('plan.max_properties') );
		$properties_current = \App\Site::findOrFail($site_id)->properties()->withTrashed()->count();
		if ( $properties_allowed < 1 || $properties_allowed > $properties_current )
		{
			return $next($request);
		}

		echo view('account.warning.properties', compact('properties_allowed','properties_current'))->render();
		exit;
		*/
	}
}
