<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class EmployeePermission
{
	public function handle($request, Closure $next, $permission, $guard = null)
	{
		if ( Auth::guard($guard)->guest() )
		{
			abort(404);
		}

		// Get site ID
		$site_id = \App\Session\Site::get('site_id', false);

		// Check max allowed
		$employees_allowed = @intval( \App\Session\Site::get('plan.max_employees') );
		$employees_current = \App\Site::findOrFail($site_id)->users()->withRole('employee')->count();
		if ( $employees_allowed < 1 || $employees_allowed > $employees_current )
		{
			return $next($request);
		}

		echo view('account.warning.employees', compact('employees_allowed','employees_current'))->render();
		exit;
	}
}
