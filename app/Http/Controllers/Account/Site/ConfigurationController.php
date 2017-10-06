<?php

namespace App\Http\Controllers\Account\Site;

use Illuminate\Http\Request;

use App\Http\Requests;

class ConfigurationController extends \App\Http\Controllers\AccountController
{

	public function __initialize()
	{
		$this->middleware([ 'permission:site-edit' ], [ 'except' => [ 'getCheck'] ]);

		parent::__initialize();
		\View::share('submenu_section', 'site');
		\View::share('submenu_subsection', 'site-configuration');
	}

	public function getIndex()
	{
		$site = $this->site;

		$max_languages = @intval( \App\Session\Site::get('plan.max_languages') );

		$currencies = \App\Models\Currency::withTranslations()->enabled()->orderBy('title')->lists('title','code')->all();

		$current_tab = session('current_tab', old('current_tab', $this->request->input('current_tab','config')));

		$timezones = \App\Site::getTimezoneOptions();

		return view('account.site.configuration.index', compact('site','max_languages','timezones','currencies','current_tab'));
	}

	public function postIndex()
	{
		// Validate general fields
		$fields = [
			'theme' => 'required|in:'.implode(',', array_keys(\Config::get('themes.themes'))),
			'site_currency' => 'required|exists:currencies,code',
			'timezone' => 'required|in:'.implode(',', \App\Site::getTimezoneOptions()),
			'customer_register' => 'required|boolean',
			'locales_array' => 'required|array',
			'i18n' => 'array',
			'i18n.title.'.fallback_lang() => 'required',
			'social_array' => 'required|array',
			'mailer' => 'required|array',
			'mailer.service' => 'required|in:default,custom',
			'mailer.from_name' => 'required',
			'mailer.from_email' => 'required|email',
			'signature' => 'required|array',
			'signature.name' => 'required|string',
			'signature.email' => 'email',
			'home_highlights' => 'required|in:0,3,6,9',
			'recaptcha_enabled' => 'required|boolean',
			'recaptcha_sitekey' => 'string',
			'recaptcha_secretkey' => 'string',
		];

		switch ( $this->request->input('mailer.service') )
		{
			case 'custom':
				$fields['mailer.out.protocol'] = 'required|in:smtp';
				$fields['mailer.out.host'] = 'required';
				$fields['mailer.out.username'] = 'required';
				$fields['mailer.out.password'] = 'required';
				$fields['mailer.out.port'] = 'required';
				$fields['mailer.out.layer'] = 'in:tls,ssl';
				$fields['mailer.in.protocol'] = 'required|in:pop3,imap,mailgun';
				$fields['mailer.in.host'] = 'required';
				$fields['mailer.in.username'] = 'required';
				$fields['mailer.in.password'] = 'required';
				switch ( $this->request->input('mailer.in.protocol') )
				{
					case 'pop3':
					case 'imap':
						$fields['mailer.in.port'] = 'required';
						break;
				}
				$fields['mailer.in.layer'] = 'in:tls,ssl';
				break;
		}

		$validator = \Validator::make($this->request->all(), $fields);
		if ($validator->fails()) 
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		// Fallback locale is always required
		$locales_array = $this->request->input('locales_array');
		if ( !in_array(fallback_lang(), $locales_array) ) 
		{
			$locales_array[] = fallback_lang();
		}

		$i18n = $this->request->input('i18n');
		$valid_locales = \LaravelLocalization::getSupportedLocales();

		// Save site
		$this->site->theme = $this->request->input('theme');
		$this->site->timezone = $this->request->input('timezone');
		$this->site->ga_account = $this->request->input('ga_account');
		$this->site->customer_register = $this->request->input('customer_register') ? 1 : 0;
		$this->site->mailer = $this->request->input('mailer');
		$this->site->alert_config = $this->request->input('alerts');
		$this->site->home_highlights = $this->request->input('home_highlights');
		$this->site->recaptcha_enabled = $this->request->input('recaptcha_enabled') ? 1 : 0;
		$this->site->recaptcha_sitekey = $this->request->input('recaptcha_sitekey');
		$this->site->recaptcha_secretkey = $this->request->input('recaptcha_secretkey');
		
		if ( $this->site->can_hide_molista )
		{
			$this->site->hide_molista = $this->request->input('hide_molista') ? 1 : 0;
		}

		$signature = $this->request->input('signature');
		foreach ($signature as $key => $value)
		{
			$signature[$key] = sanitize($value);
		}
		$this->site->signature = $signature;

		// Save logo && favicon
		foreach ( [ 'logo','favicon' ] as $img_key )
		{
			if ( !$this->request->hasFile($img_key) )
			{
				continue;
			}

			// Validate image
			if ( $img_key == 'favicon' )
			{
				$fields = [
					$img_key => 'required|mimes:ico|max:' . \Config::get('app.property_image_maxsize', 2048),
				];
			}
			else
			{
				$fields = [
					$img_key => 'required|image|max:' . \Config::get('app.property_image_maxsize', 2048),
				];
			}
			$validator = \Validator::make($this->request->all(), $fields);
			if ($validator->fails()) 
			{
				continue;
			}

			$img_folder = public_path("sites/{$this->site->id}");

			$img_name = $this->request->file($img_key)->getClientOriginalName();
			while ( file_exists("{$img_folder}/{$img_name}") )
			{
				$img_name = uniqid() . '_' . $this->request->file($img_key)->getClientOriginalName();
			}

			$this->request->file($img_key)->move($img_folder, $img_name);

			if ( @$this->site->$img_key )
			{
				@unlink("{$img_folder}/{$this->site->$img_key}");
			}

			$this->site->$img_key = $img_name;
		}

		// Save locales
		$this->site->locales()->detach();
		$locales = \App\Models\Locale::whereIn('locale', $locales_array)->lists('id', 'locale')->toArray();
		foreach ($locales_array as $locale) 
		{
			if ( array_key_exists($locale, $locales) )
			{
				$this->site->locales()->attach($locales[$locale]);
			}
		}

		// Save i18n
		foreach ($i18n as $key=>$translations)
		{
			if ( !in_array($key, $this->site->translatedAttributes) )
			{
				continue;
			}
			foreach ($translations as $iso=>$def)
			{
				$this->site->translateOrNew($iso)->$key = sanitize($def);
			}
		}

		// Save social media
		foreach ($this->request->input('social_array') as $network => $network_url)
		{
			$social_media = $this->site->social()->firstOrCreate([
				'network' => $network,
			]);

			if ( !$social_media )
			{
				 continue;
			}

			$social_media->update([
				'url' => sanitize($network_url, 'url'),
			]);
		}

		// Currency has changed?
		if ( $this->request->input('site_currency') && $this->request->input('site_currency') != $this->site->site_currency )
		{
			// Update site currency
			$this->site->site_currency =  $this->request->input('site_currency');
			// Update properties currency
			$this->site->properties()->withTrashed()->update([
				'currency' => $this->request->input('site_currency'),
			]);
		}

		// Save configuration
		$this->site->save();

		return redirect()->back()->with('current_tab', $this->request->input('current_tab'))->with('success', trans('account/site.configuration.saved'));
	}

	public function getCheck($type)
	{
		$error = true;

		switch ( $type ) {
			case 'domain':
				$domain = rtrim($this->request->input('domain'), '/');
				// If domain is same as molista domain, false
				if ( preg_match('#\.'.\Config::get('app.application_domain').'(\/)?$#', $domain) )
				{
					break;
				}
				$query = \App\SiteDomains::where('domain', $domain);
				if ( $this->request->input('id') )
				{
					$query->where('id', '!=', $this->request->input('id'));
				}
				$error = $query->count();
				break;
			case 'subdomain':
				$query = \App\Site::where('subdomain', $this->request->input('subdomain'));
				if ( $this->request->input('id') )
				{
					$query->where('id', '!=', $this->request->input('id'));
				}
				$error = $query->count();
				break;
		}

		echo $error ? 'false' : 'true';
	}

	public function postTestMailerConfiguration()
	{
		// Validate general fields
		$fields = [
			'protocol' => 'required',
		];
		$validator = \Validator::make($this->request->all(), $fields);
		if ($validator->fails()) 
		{
			return [ 'error' => true ];
		}

		if ( $this->site->ticket_adm->testEmail( $this->request->input('protocol') ) )
		{
			return [ 'success' => true ];
		}
		
		return [ 'error' => true ];
	}

}
