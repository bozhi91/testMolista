<?php

namespace App\Http\Middleware;

use Closure;

class SiteSetupUser
{
	public function handle($request, Closure $next)
	{

		// No user, forget
		if ( \Auth::guest() )
		{
			session()->forget( \Config::get('app.user_session_name') );
		}

		$user = session( \Config::get('app.user_session_name') );
		if ( !empty($user['user_id']) &&  $user['user_id'] != \Auth::user()->id )
		{
			session()->forget( \Config::get('app.user_session_name') );
		}

		return $next($request);
	}
}
