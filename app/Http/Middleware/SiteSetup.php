<?php

namespace App\Http\Middleware;

use Closure;

class SiteSetup
{
	public function handle($request, Closure $next)
	{
		// Get current site
		$site = \App\Site::with('locales')->enabled()->current()->first();
		if ( !$site ) 
		{
			abort(404);
		}

		// Get site setup
		$setup = $site->site_setup;
		\App\Session\Site::replace($setup);

		// Add site to request attributes
		$request->attributes->add([
			'site' => $site,
		]);

		// Share site to views
		\View::share('current_site', $site);

		// Set theme
		if ( !empty($setup['theme']) )
		{
			\Theme::set($setup['theme']);
		}

		// Set redirection after user login
		putenv( "afterLoginRedirectTo=" . action('Account\TicketsController@getIndex') );

		// Set required site
		putenv( "loginRequiredSite={$setup['site_id']}" );

		// Share setup through all views
		\View::share('site_setup', $setup);

		// Check if language is enabled
		if ( !array_key_exists(\LaravelLocalization::getCurrentLocale(), $setup['locales']) )
		{
			// Redirect first enabled language
			$locales = array_keys($setup['locales']);
			return redirect()->to( \LaravelLocalization::getLocalizedURL(array_shift($locales), url()->full() ) );
			exit;
		}

		// Check if plan is valid
		$is_valid = empty($setup['plan']['is_valid']) ? false : true;
		if ( !$is_valid )
		{
			// Always enabled routes
			$allowed_paths = [
				'account/*', '*/account/*', 'account', '*/account',
				'login', '*/login',
				'logout', '*/logout',
			];

			$error = true;
			foreach ($allowed_paths as $allowed)
			{
				if ( $request->is($allowed) )
				{
					$error = false;
					break;
				}
			}

			if ( $error )
			{
				echo view('web.suspended')->render();
				exit;
			}
		}

		return $next($request);
	}
}
