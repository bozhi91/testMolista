<?php

namespace App\Http\Middleware;

use Closure;

class SiteSetup
{
	public function handle($request, Closure $next)
	{
		$current_site = \App\Site::with('locales')->enabled()->current()->first();
		if ( !$current_site ) 
		{
			abort(404);
		}

		$setup = $current_site->site_setup;
		\App\Session\Site::replace($setup);

		// Set theme
		if ( !empty($setup['theme']) )
		{
			\Theme::set($setup['theme']);
		}

		// Set redirection after user login
		putenv( "afterLoginRedirectTo=" . action('AccountController@index') );

		// Set required site
		putenv( "loginRequiredSite={$setup['site_id']}" );

		// Share setup through all views
		\View::share('site_setup', $setup);

		// Check if language is enabled
		if ( !array_key_exists(\LaravelLocalization::getCurrentLocale(), $setup['locales']) )
		{
			abort(404);
		}

		return $next($request);
	}
}
