<?php

namespace App\Http\Middleware;

use Closure;

class SiteSetup
{
	public function handle($request, Closure $next)
	{
		// Check if session exists
		$setup = session()->get('site_setup');

		if ( env('APP_DEBUG') || !$setup )
		{
			$site_setup = \App\Site::with('locales')->enabled()->current()->first();

			if ( !$site_setup ) 
			{
				abort(404);
			}

			$setup = [
				'site_id' => $site_setup->id,
				'theme' => $site_setup->theme,
				'logo' => $site_setup->logo ? asset("sites/{$site_setup->id}/{$site_setup->logo}") : false,
				'favicon' => $site_setup->favicon ? asset("sites/{$site_setup->id}/{$site_setup->favicon}") : false,
				'locales' => [],
				'locales_select' => [],
				'social_media' => $site_setup->social_array,
				'seo' => $site_setup->i18n,
			];

			foreach ($site_setup->locales as $locale)
			{
				$setup['locales'][$locale->locale] = [
					'locale' => $locale->locale,
					'flag' => $locale->flag,
					'dir' => $locale->dir,
					'name' => $locale->name,
					'script' => $locale->script,
					'native' => $locale->native,
					'regional' => $locale->regional,
				];
				$setup['locales_select'][$locale->locale] = $locale->native;
			}

			session()->put('site_setup', $setup);
		}

		if ( !$setup ) 
		{
			abort(404);
		}

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
		$current_locale = \LaravelLocalization::getCurrentLocale();
		if ( !array_key_exists($current_locale, $setup['locales']) )
		{
			abort(404);
		}

		return $next($request);
	}
}
