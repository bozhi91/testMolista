<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class SiteAutologin {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next) {

		if ( !$request->input('autologin_token') ) 
		{
			return $next($request);
		}

		// Logout
		Auth::logout();

		// Build redirect url
		$query = $request->query();
		unset($query['autologin_token']);
		$redirect = url_current() . ( empty($query) ? '' : '?'.http_build_query($query) );

		// Get the site
		$site = $request->get('site');
		if ( !$site )
		{
			return redirect()->to($redirect);
		}

		// Verify user belongs to site
		$user = $site->users()->where('autologin_token', $request->input('autologin_token'))->first();
		if ( !$user ) 
		{
			return redirect()->to($redirect);
		}

		// Delete token
		$user->autologin_token = null;
		$user->save();

		// Autologin
		Auth::loginUsingId($user->id);

		// Build redirect url
		$query = $request->query();
		unset($query['autologin_token']);
		$redirect = url_current() . ( empty($query) ? '' : '?'.http_build_query($query) );

		// Redirect
		return redirect()->to($redirect);
	}

}
