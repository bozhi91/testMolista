<?php namespace App\Http\Middleware;

use Closure;

class SiteSetup
{
	public function handle($request, Closure $next)
	{
		// Get current site
		$site = \App\Site::enabled()
			->with('locales')
			->with('infocurrency')
			->current()->first();
		if ( !$site ) 
		{
			return $this->checkRedirection();
		}

		// Get site setup
		$setup = $site->site_setup;
		\App\Session\Site::replace($setup);
		$site->setRecaptchaConfig();

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
		putenv( "afterLoginRedirectTo=" . action('Account\ReportsController@getIndex') );

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
        $site->verifyPlan($site);

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
				//echo view('web.suspended')->render();
				//exit;
			}
		}

		return $next($request);
	}

	public function checkRedirection()
	{
		$parts = parse_url( url_current() );

		if ( preg_match('#^www.#', $parts['host']) )
		{
			$host = substr($parts['host'], 4);
		}
		else
		{
			$host = "www.{$parts['host']}";
		}

		$site = \App\SiteDomains::where('domain', "{$parts['scheme']}://{$host}")->first();
		if ( $site )
		{
			$redirect_url = "{$parts['scheme']}://{$host}" . @$parts['path'] . (empty($parts['query']) ? '' : "?{$parts['query']}");
			return redirect()->away($redirect_url);
		}

		abort(404);
	}

}
