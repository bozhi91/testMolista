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
		$site = $this->auth->user()->sites()->withTranslations()->with('social')->findOrFail( $this->site->id );

		return view('account.site.configuration.index', compact('site'));
	}

	public function postIndex()
	{
		$site = $this->auth->user()->sites()->withTranslations()->findOrFail( $this->site->id );

		// Replace subdomain and domains
		$current_domains = $site->domains->lists('domain','id')->toArray();
		if ( !$current_domains )
		{
			$current_domains = [ 'new'=>'' ];
		}
		$this->request->merge([
			'subdomain' => $site->subdomain,
			'domains_array' => $current_domains,
		]);

		// Validate general fields
		$fields = [
			'subdomain' => 'required|alpha_dash',
			'theme' => 'required|in:'.implode(',', array_keys(\Config::get('themes.themes'))),
			'customer_register' => 'required|boolean',
			'locales_array' => 'required|array',
			'i18n' => 'array',
			'i18n.title.'.fallback_lang() => 'required',
			'domains_array' => 'required|array',
			'social_array' => 'required|array',
			'mailer' => 'required|array',
			'mailer.service' => 'required|in:default,custom',
			'mailer.from_name' => 'required',
			'mailer.from_email' => 'required|email',
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
			return \Redirect::back()->withInput()->withErrors($validator);
		}

		// Fallback locale is always required
		$locales_array = $this->request->get('locales_array');
		if ( !in_array(fallback_lang(), $locales_array) ) 
		{
			$locales_array[] = fallback_lang();
		}

		$i18n = $this->request->get('i18n');
		$valid_locales = \LaravelLocalization::getSupportedLocales();

		// Validate subdomain
		if ( \App\Site::where('subdomain', $this->request->get('subdomain'))->where('id','!=',@intval($site->id))->count() )
		{
			return \Redirect::back()->withInput()->with('error', trans('account/site.configuration.subdomain.error'));
		}

		// Validate domains
		foreach ( $this->request->get('domains_array') as $id => $domain )
		{

			$domain = rtrim($domain, '/');
			// If domain is same as molista domain, false
			if ( preg_match('#\.'.\Config::get('app.application_domain').'(\/)?$#', $domain) || \App\SiteDomains::where('domain', $domain)->where('id','!=',@intval($id))->count() )
			{
				return \Redirect::back()->withInput()->with('error', trans('account/site.configuration.domains.error'));
			}
		}

		// Save site
		$site->subdomain = $this->request->get('subdomain');
		$site->theme = $this->request->get('theme');
		$site->customer_register = $this->request->get('customer_register') ? 1 : 0;
		$site->mailer = $this->request->get('mailer');

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

			$img_folder = public_path("sites/{$site->id}");

			$img_name = $this->request->file($img_key)->getClientOriginalName();
			while ( file_exists("{$img_folder}/{$img_name}") )
			{
				$img_name = uniqid() . '_' . $this->request->file($img_key)->getClientOriginalName();
			}

			$this->request->file($img_key)->move($img_folder, $img_name);

			if ( !empty($site->$img_key) )
			{
				@unlink("{$img_folder}/{$site->$img_key}");
			}

			$site->$img_key = $img_name;
		}

		// Save locales
		\DB::table('sites_locales')->where('site_id',$site->id)->delete();
		$locales = \App\Models\Locale::whereIn('locale', $locales_array)->lists('id', 'locale')->toArray();
		foreach ($locales_array as $locale) 
		{
			if ( array_key_exists($locale, $locales) )
			{
				$site->locales()->attach($locales[$locale]);
			}
		}

		// Save i18n
		foreach ($i18n as $key=>$translations)
		{
			if ( !in_array($key, $site->translatedAttributes) )
			{
				continue;
			}
			foreach ($translations as $iso=>$def)
			{
				$site->translateOrNew($iso)->$key = sanitize($def);
			}
		}

		// Save domains
		foreach ($this->request->get('domains_array') as $domain_id => $domain_url)
		{
			// Remove empty spaces and triling slash
			$domain_url = rtrim(trim($domain_url), '/');

			if ( $domain_url && $domain_id == 'new' )
			{
				$site->domains()->create([
					'domain' => sanitize($domain_url, 'url'),
				]);
			}
			elseif ( $site_domain = $site->domains()->find($domain_id) )
			{
				if ( $domain_url )
				{
					$site_domain->update([
						'domain' => sanitize($domain_url, 'url'),
					]);
				}
				else
				{
					$site_domain->delete();
				}
			}
		}

		// Save social media
		foreach ($this->request->get('social_array') as $network => $network_url)
		{
			$social_media = $site->social()->firstOrCreate([
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

		// Save configuration
		$site->save();

		// Remove from session
		\App\Session\Site::flush();

		return \Redirect::back()->with('current_tab', $this->request->get('current_tab'))->with('success', trans('account/site.configuration.saved'));
	}

	public function getCheck($type)
	{
		$error = true;

		switch ( $type ) {
			case 'domain':
				$domain = rtrim($this->request->get('domain'), '/');
				// If domain is same as molista domain, false
				if ( preg_match('#\.'.\Config::get('app.application_domain').'(\/)?$#', $domain) )
				{
					break;
				}
				$query = \App\SiteDomains::where('domain', $domain);
				if ( $this->request->get('id') )
				{
					$query->where('id', '!=', $this->request->get('id'));
				}
				$error = $query->count();
				break;
			case 'subdomain':
				$query = \App\Site::where('subdomain', $this->request->get('subdomain'));
				if ( $this->request->get('id') )
				{
					$query->where('id', '!=', $this->request->get('id'));
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
