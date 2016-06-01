<?php

namespace App\Http\Middleware;

use Closure;

class SiteSetup
{
	public function handle($request, Closure $next)
	{
		// Current locale
		$current_locale = \LaravelLocalization::getCurrentLocale();

		// Check if session exists
		$setup = \App\Session\Site::all();

		if ( !$setup || $setup['locale'] != $current_locale )
		{

			$site_setup = \App\Site::with('locales')->enabled()->current()->first();

			if ( !$site_setup ) 
			{
				abort(404);
			}

			$setup = [
				'site_id' => $site_setup->id,
				'locale' => $current_locale,
				'theme' => $site_setup->theme,
				'logo' => $site_setup->logo ? asset("sites/{$site_setup->id}/{$site_setup->logo}") : false,
				'favicon' => $site_setup->favicon ? asset("sites/{$site_setup->id}/{$site_setup->favicon}") : false,
				'locales' => [],
				'locales_select' => [],
				'locales_tabs' => [],
				'social_media' => $site_setup->social_array,
				'seo' => $site_setup->i18n,
				'widgets' => [
					'header' => [],
					'footer' => [],
				],
			];

			foreach ($site_setup->locales->sortBy('native') as $locale)
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

			$setup['locales_tabs'] = [];
			if ( array_key_exists(fallback_lang(), $setup['locales_select']) )
			{
				$setup['locales_tabs'][fallback_lang()] = $setup['locales_select'][fallback_lang()];
			}

			foreach ($setup['locales_select'] as $locale => $locale_name) 
			{
				$setup['locales_tabs'][$locale] = $locale_name;
			}

			$widgets = $site_setup->widgets()->withTranslations()->withMenu()->orderBy('position')->get();
			foreach ($widgets as $widget)
			{
				$setup['widgets'][$widget->group][$widget->id] = $widget;
			}

			\App\Session\Site::replace($setup);
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
		putenv( "afterLoginRedirectTo=" . action('Account\TicketsController@getIndex') );

		// Set required site
		putenv( "loginRequiredSite={$setup['site_id']}" );

		// Share setup through all views
		\View::share('site_setup', $setup);

		// Check if language is enabled
		if ( !array_key_exists($current_locale, $setup['locales']) )
		{
			abort(404);
		}

		return $next($request);
	}
}
