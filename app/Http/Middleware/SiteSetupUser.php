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
			\App\Session\User::flush();
			return $next($request);
		}

		$user = \App\Session\User::all();
		if ( !empty($user['user_id']) &&  $user['user_id'] != \Auth::user()->id )
		{
			\App\Session\User::flush();
		}

		\App\Session\User::put('user_id', \Auth::user()->id);

		if ( $site = $request->get('site') )
		{
			$site_user = $site->users()->with([ 'properties' => function($query) use ($site) {
				$query->ofSite( $site->id );
			}])->find( \Auth::user()->id );

			if ( $site_user )
			{
				$request->attributes->add([
					'site_user' => $site_user,
				]);
				\View::share('current_site_user', $site_user);
			}
		}

		return $next($request);
	}
}
