<?php namespace App;

use \App\TranslatableModel;
use Laravel\Cashier\Billable;
use App\Models\Site\Subscription;

use Swift_Mailer;

class Site extends TranslatableModel
{
    use Billable;

	public $translatedAttributes = ['title','subtitle','description'];

	protected $table = 'sites';
	protected $guarded = [];

	protected $casts = [
		'signature' => 'array',
		'invoicing' => 'array',
		'country_ids' => 'array',
		'alert_config' => 'array',
	];

	protected $data;

	protected $ticket_token = false;

	public static function boot()
	{
		parent::boot();

		// Whenever a site is updated
		static::updated(function($site){
			$site->updateSiteSetup();
			$site->ticket_adm->updateSite();
		});
	}

	public function plan()
	{
		return $this->belongsTo('App\Models\Plan');
	}

	public function reseller()
	{
		return $this->belongsTo('App\Models\Reseller')->with('plans');
	}

	public function country()
	{
		return $this->hasOne('App\Models\Geography\Country','code','country_code')->withTranslations();
	}

	public function imports()
	{
		return $this->hasMany('App\Models\Site\Import');
	}

	public function districts()
	{
		return $this->hasMany('App\Models\Site\District');
	}
	
	public function planchanges()
	{
		return $this->hasMany('App\Models\Site\Planchange');
	}

	public function priceranges()
	{
		return $this->hasMany('App\Models\Site\Pricerange')->withTranslations();
	}

	public function slidergroups()
	{
		return $this->hasMany('App\Models\Site\SliderGroup');
	}

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'site_id')->orderBy('created_at', 'desc');
    }

	public function webhooks()
	{
		return $this->hasMany('App\Models\Site\Webhook');
	}

	public function invoices()
	{
		return $this->hasMany('App\Models\Site\Invoice');
	}

	public function payments()
	{
		return $this->hasMany('App\Models\Site\Payment');
	}

	public function stats()
	{
		return $this->hasMany('App\Models\Site\Stats');
	}

	public function events()
	{
		return $this->hasMany('App\Models\Calendar');
	}

	public function users()
	{
		return $this->belongsToMany('App\User', 'sites_users', 'site_id', 'user_id')->withPivot('can_create','can_edit','can_delete','can_view_all','can_edit_all','can_delete_all');
	}
	public function getUsersIdsAttribute()
	{
		$users = [];

		foreach ($this->users as $user)
		{
			$users[] = $user->id;
		}

		return $users;
	}
	public function getOwnersIdsAttribute()
	{
		return \App\User::withRole('company')->whereIn('id', $this->users_ids)->lists('id')->toArray();
	}
	public function getEmployeesIdsAttribute()
	{
		return \App\User::withRole('employee')->whereIn('id', $this->users_ids)->lists('id')->toArray();
	}

	public function users_signatures() {
		return $this->hasMany('\App\Models\Site\UserSignature');
	}

	public function users_since() {
		return $this->hasMany('\App\Models\Site\UserSince');
	}

	public function customers() {
		return $this->hasMany('App\Models\Site\Customer');
	}
	public function getCustomersOptionsAttribute()
	{
		return $this->getCustomersOptions();
	}
	public function getCustomersOptions($user=false)
	{
		$options = [];

		$query = $this->customers()->orderBy('first_name')->orderBy('last_name')->orderBy('email');

		if ( $user && $user->hasRole('employee') )
		{
			$query->ofUser($user->id);
		}

		$customers = $query->get();

		foreach ($customers as $customer)
		{
			$options[$customer->id] = "{$customer->full_name} ({$customer->email})";
		}

		return $options;
	}

	public function properties()
	{
		return $this->hasMany('App\Property')->with('infocurrency')->withTranslations();
	}

	public function getTransactions()
	{
		$property_ids = $this->properties()->pluck('id')->toArray();
		return Models\Property\Catches::whereIn('property_id', $property_ids);
	}
	
	public function api_keys()
	{
		return $this->hasMany('App\Models\ApiKey');
	}

	public function menus()
	{
		return $this->hasMany('App\Models\Site\Menu');
	}

	public function widgets()
	{
		return $this->hasMany('App\Models\Site\Widget')->withTranslations();
	}

	public function pages()
	{
		return $this->hasMany('App\Models\Site\Page')->withTranslations();
	}

	public function social()
	{
		return $this->hasMany('App\SiteSocial');
	}
	public function getSocialArrayAttribute()
	{
		$networks = [];

		foreach ($this->social as $social)
		{
			if ( !$social->url )
			{
				continue;
			}

			$networks[$social->network] = $social->url;
		}

		return $networks;
	}

	public function domains()
	{
		return $this->hasMany('App\SiteDomains');
	}
	public function getDomainsArrayAttribute()
	{
		$domains = [];

		foreach ($this->domains as $domain)
		{
			$domains[$domain->id] = $domain->domain;
		}

		return $domains;
	}
	public function getDomainDefaultAttribute() {
		return ( count($this->domains) < 1 ) ? false : $this->domains->sortByDesc('default')->first()->domain;
	}

	public function locales()
	{
		return $this->belongsToMany('App\Models\Locale', 'sites_locales', 'site_id', 'locale_id');
	}
	public function getLocalesArrayAttribute()
	{
		$locales = [];

		foreach ($this->locales as $locale)
		{
			$locales[] = $locale->locale;
		}

		return $locales;
	}

	public function infocurrency()
	{
		return $this->hasOne('App\Models\Currency', 'code', 'site_currency')->withTranslations();
	}
	public function infositecurrency()
	{
		return $this->hasOne('App\Models\Currency', 'code', 'site_currency')->withTranslations();
	}
	public function infopaymentcurrency()
	{
		return $this->hasOne('App\Models\Currency', 'code', 'payment_currency')->withTranslations();
	}

	public function marketplaces()
	{
		return $this->belongsToMany('App\Models\Marketplace', 'sites_marketplaces', 'site_id', 'marketplace_id')
					->withPivot('marketplace_configuration','marketplace_enabled','marketplace_maxproperties','marketplace_export_all');
	}
	public function getMarketplacesArrayAttribute()
	{
		$marketplaces = [];

		foreach ($this->marketplaces as $marketplaces)
		{
			$marketplaces[] = $marketplaces->id;
		}

		return $marketplaces;
	}

	public function getMainUrlAttribute()
	{
		if ( count($this->domains) < 1 )
		{
			return \Config::get('app.application_protocol') . "://{$this->subdomain}." . \Config::get('app.application_domain');
		}

		return $this->domains->sortByDesc('default')->first()->domain;
	}

	public function getAccountUrlAttribute()
	{
		return str_replace(
			rtrim(\Config::get('app.application_url'),'/'), //replaces current url
			rtrim($this->main_url,'/'), // with website url
			action('AccountController@index')
		);
	}

	public function getAutologinUrlAttribute()
	{
		$owners_ids = $this->owners_ids;

		if ( empty($owners_ids) )
		{
			return false;
		}

		$owner = \App\User::find( array_shift($owners_ids) );
		if ( !$owner )
		{
			return false;
		}

		// Set owner autologin_token
		$autologin_token = $owner->getUpdatedAutologinToken();

		// Return autologin url
		$url = action('Account\ReportsController@getIndex', [ 'autologin_token' =>$autologin_token ]);

		// Subdomain url
		return str_replace(
					rtrim(\Config::get('app.application_url'),'/'), //replaces current url
					\Config::get('app.application_protocol') . "://{$this->subdomain}." . \Config::get('app.application_domain'), // with website url
					$url
				);

		// Domain/subdomain
		//return $this->getSiteFullUrl( $url );
	}

	public function getRememberPasswordUrlAttribute()
	{
		$url = action('Auth\PasswordController@reset');
		return $this->getSiteFullUrl( $url );
	}

	public function getSiteFullUrl($url)
	{
		return str_replace(
					rtrim(\Config::get('app.application_url'),'/'), //replaces current url
					rtrim($this->main_url,'/'), // with website url
					$url
				);
	}

	public function getXmlPathAttribute()
	{
		return storage_path("sites/{$this->id}/xml");
	}
	public function getXmlPropertiesFeedPath($marketplace)
	{
		return $this->getXmlFeedPath($marketplace,'properties');
	}
	public function getXmlOwnersFeedPath($marketplace)
	{
		return $this->getXmlFeedPath($marketplace,'owners');
	}
	public function getXmlFeedPath($marketplace,$type)
	{
		return "{$this->xml_path}/{$marketplace}/{$type}.xml";
	}
	public function getXmlFeedUrl($marketplace,$type)
	{
		if (!$this->main_url)
		{
			return false;
		}

        if ($type == 'properties') {
            $adm = \App\Models\Marketplace::where('code', $marketplace)->where('enabled', 1)->first();
            if ($adm) {
                $helper = $this->MarketplaceHelper;
                $helper->setMarketplace($adm);

                $url = $helper->getMarketplaceAdm()->getFeedUrl();
            }
        }

        if (!empty($url)) {
            return implode('/',[
    			rtrim($this->main_url, '/'),
    			'feeds',
    			$type,
                $marketplace,
    			$url
    		]);
        }

		return implode('/',[
			rtrim($this->main_url, '/'),
			'feeds',
			$type,
			"{$marketplace}.xml",
		]);
	}

	public function getSignaturePartsAttribute()
	{
		$signature = $this->signature;

		if ( !is_array($signature) )
		{
			$signature = [];
		}

		$signature['url'] = $this->main_url;

		return $signature;
	}

	public function setMailerAttribute($value)
	{
		$this->attributes['mailer'] = @serialize($value);
	}
	public function getMailerAttribute($value)
	{
		$unserialized = @unserialize($value);
		return is_array($unserialized) ? $unserialized : [];
	}
	public function getMailerServiceAttribute()
	{
		return @$this->mailer['service'];
	}
	public function getMailerOutAttribute()
	{
		$protocol = @$this->mailer['out']['protocol'];

		if ( !$protocol || $this->mailer_service != 'custom' )
		{
			return false;
		}

		$out = @$this->mailer['out'];
		if ( !$out ) $out = [];

		return array_merge($out, [
			'from_name' => $this->mailer['from_name'],
			'from_email' => $this->mailer['from_email'],
		]);
	}
	public function getMailerInAttribute()
	{
		$protocol = @$this->mailer['in']['protocol'];

		if ( !$protocol || $this->mailer_service != 'custom' )
		{
			return false;
		}

		return @$this->mailer['in'];
	}

	public function setTicketToken($token)
	{
		$this->ticket_token = $token;
	}

	public function getTicketAdmAttribute()
	{
		return new \App\Models\Site\TicketAdm( $this->id, $this->ticket_token );
	}

	public function getHasPendingPlanRequestAttribute()
	{
		return $this->planchanges()->pending()->count();
	}

	public function scopeEnabled($query)
	{
		return $query->where("{$this->getTable()}.enabled", 1);
	}

	public function scopeWithDomain($query,$domain)
	{
		return $query->where(function ($query) use ($domain) {
			$query->whereIn('sites.id', function($query) use ($domain) {
				$query->select('site_id')
					->from('sites_domains')
					->where('domain', 'like', "%{$domain}%");
			})->orWhere('subdomain', 'like', "%{$domain}%");
		});
	}

	public function scopeCurrent($query)
	{
		// Parse url
		$parts = parse_url( url_current() );

		// No host, no results
		if ( empty($parts['host']) )
		{
			return $query->whereRaw('1=2');
		}

		// Check if it's a subdomain
		if ( preg_match('#^(.*)\.'.\Config::get('app.application_domain').'$#', $parts['host'], $matches))
		{
			return $query->where("{$this->getTable()}.subdomain", $matches[1]);
		}

		// Check if it's a domain
		$site_id = \App\SiteDomains::where('domain', "{$parts['scheme']}://{$parts['host']}")->value('site_id');
		if ( $site_id )
		{
			return $query->where("{$this->getTable()}.id", $site_id);
		}

		// None of the previous options
		return $query->whereRaw('1=2');
	}

	public function getSiteMailerParams()
	{
		$params = [
			'backup_required' => true,
			'service' => $this->mailer_service,
			'from_email' => @$this->mailer['from_email'],
			'from_name' => @$this->mailer['from_name'],
			'reply_email' => @$this->mailer['from_email'],
			'reply_name' => @$this->mailer['from_name'],
		];

		switch ( $this->mailer_service )
		{
			case 'custom':
				break;
			default:
				$params['backup_required'] = false;
				$params['from_email'] = env('MAIL_FROM_EMAIL','no-reply@molista.com');
				break;
		}

		if ( !$params['from_email'] )
		{
			$params['from_email'] = 'no-reply@molista.com';
		}

		if ( !$params['reply_email'] )
		{
			$params['reply_email'] = 'no-reply@molista.com';
		}

		return $params;
	}

	public function getSiteMailerClient()
	{
		switch ( $this->mailer_service )
		{
			case 'custom':
				$transport = \Swift_SmtpTransport::newInstance(@$this->mailer['out']['host'], @$this->mailer['out']['port'], @$this->mailer['out']['layer']);
				$transport->setUsername(@$this->mailer['out']['username']);
				$transport->setPassword(@$this->mailer['out']['password']);
				$transport->setStreamOptions([
					'ssl' => [
						'verify_peer' => false,
						'verify_peer_name' => false,
						'allow_self_signed' => true,
					],
				]);
				return new Swift_Mailer($transport);
			default:
				return \Mail::getSwiftMailer();
		}
	}

	public function getSiteSetupAttribute()
	{
		$path = $this->getSiteSetupPath();

		if ( !file_exists($path) )
		{
			$this->updateSiteSetup();
		}

		// Get setup
		$setup = include $path;

		// Check if update is required (every 24 hours)
		if ( !isset($setup['last_updated']) || strtotime($setup['last_updated']) < time()-(60*60*24) )
		{
			$this->updateSiteSetup();
			$setup = include $path;
		}

		// Set current locale
		$setup['locale'] = \App::getLocale();

		// Plan valid
		if ( $setup['plan']['is_free'] ) {
			$setup['plan']['is_valid'] = 1;
		} elseif ( !$setup['plan']['paid_until'] ) {
			$setup['plan']['is_valid'] = 1;
		} elseif ( strtotime($setup['plan']['paid_until']) + (60*60*24) > time() ) {
			$setup['plan']['is_valid'] = 1;
		} else {
			$setup['plan']['is_valid'] = 0;
		}

		// Locales
		$setup['locales_select'] = [];
		foreach ($setup['locales'] as $locale => $attr)
		{
			$setup['locales_select'][$locale] = $attr['native'];
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

		// Widgets
		if ( empty($setup['widgets'][$setup['locale']]) )
		{
			$setup['widgets'] = [
				'header' => [],
				'footer' => [],
			];
		}
		else
		{
			$setup['widgets'] = $setup['widgets'][$setup['locale']];
		}

		return $setup;
	}
	public function updateSiteSetup()
	{
		// Locale backup
		$locale_backup = \App::getLocale();

		// Site updated
		$site = self::find( $this->id );

		// Site information
		$setup = [
			'site_id' => $site->id,
			'last_updated' => date("Y-m-d H:i:s"),
			'theme' => $site->theme,
			'logo' => $site->logo ? asset("sites/{$site->id}/{$site->logo}") : false,
			'favicon' => $site->favicon ? asset("sites/{$site->id}/{$site->favicon}") : false,
			'social_media' => $site->social_array,
			'seo' => $site->i18n,
		];

		// Plan & payment
		$setup['plan'] = array_merge(
			$site->plan ? $site->plan->toArray() : \App\Models\Plan::where('code','free')->first()->toArray(),
			[
				'payment_method' => $site->payment_method,
				'iban_account' => $site->iban_account,
				'stripe_id' => $site->stripe_id,
				'paid_until' => $site->paid_until,
				'card_brand' => $site->card_brand,
				'card_last_four' => $site->card_last_four,
			]
		);

		// Pending planc hange request
		$pending_request = $this->planchanges()->pending()->with('plan')->first();
		$setup['pending_request'] = $pending_request ? $pending_request->toArray() : false;

		// Locales
		$setup['locales'] = [];
		$locales = $site->locales->sortBy('native');
		foreach ($locales as $locale)
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

		// Widgets
		$setup['widgets'] = [];
		foreach ($setup['locales'] as $locale => $attr)
		{
			\App::setLocale($locale);
			$widgets = $site->widgets()->withMenu()->orderBy('position')->get();
			foreach ($widgets as $widget)
			{
				$w = [
					'group' => $widget->group,
					'type' => $widget->type,
					'title' => $widget->title,
					'content' => $widget->content,
					'data' => $widget->data,
				];

				switch ( $widget->type )
				{
					case 'menu':
						$w['items'] = [];
						if ( $widget->menu )
						{
							foreach ($widget->menu->items as $item)
							{
								if ( $item->type == 'custom' )
								{
									$url = $item->url;
								}
								else
								{
									$url_parts = parse_url(\LaravelLocalization::getLocalizedURL($locale,$item->item_url));
									$url = implode('?', array_filter([
										@$url_parts['path'],
										@$url_parts['query'],
									]));
								}
								$w['items'][] = [
									'title' => $item->item_title,
									'url' => $url,
									'target' => $item->target,
								];
							}
						}
						break;
					case 'slider':
						$w['items'] = [];
						if ( $widget->slider )
						{
							$images = $widget->slider->images()
									->orderBy('position', 'asc')->get();

							foreach ($images as $image)
							{
								$w['items'][] = [
									'image' => $image->image,
									'link' => $image->link,
								];
							}
						}
						break;
					case 'text':
					default:
						break;
				}

				$setup['widgets'][$locale][$widget->group][$widget->id] = $w;
			}
		}
		\App::setLocale($locale_backup);

		$contents = '<?' . "php\n";
		$contents .= "// site setup - created on " . date("Y-m-d H:i:s") . "\n";
		$contents .= "return " . var_export($setup, true) . ";\n";

		return \File::put($site->getSiteSetupPath(), $contents);
	}
	public function getSiteSetupPath()
	{
		$dir = storage_path("sites/{$this->id}");
		if ( !is_dir( $dir ) )
		{
			\File::makeDirectory($dir, 0777, true, true);
		}

		return "{$dir}/setup.php";
	}

	public function sendEmail($params)
	{
		$errors = array_filter([
			empty($params['to']) ? 'receiver email' : '',
			empty($params['subject']) ? 'email subject' : '',
			empty($params['content']) ? 'email content' : '',
		]);
		if ( count($errors) > 0 )
		{
			\Log::error("Mail could not be send because some parameters are missing: " . implode(', ', $errors) );
			return false;
		}

		$params = array_merge($params, $this->getSiteMailerParams());

		$from_name = $params['from_name'];
		$from_email = $params['from_email'];
		if ( !$from_name || !$from_email )
		{
			$errors = [
				$from_name ? '' : 'sender name',
				$from_email ? '' : 'sender email',
			];
			\Log::error("Mail could not be send because some configuration is missing: " . implode(', ', array_filter($errors)) );
			return false;
		}

		if ( !$params['service'] )
		{
			\Log::error("Mailer service is not defined for site ID {$this->id}");
			return false;
		}

		// Backup current mail configuration
		if ( $params['backup_required'] )
		{
			$backup = \Mail::getSwiftMailer();
		}

		// Update configuration
		switch ( $params['service'] )
		{
			case 'custom':
				\Mail::setSwiftMailer($this->getSiteMailerClient());
				break;
			case 'default':
				break;
			default:
				\Log::error("Mailer service '{$params['service']}' is not valid for site ID {$this->id}");
				return false;
		}

		// Send email
        try {
            $res = \Mail::send('dummy', [ 'content' => $params['content'] ], function ($message) use ($params) {
                $message->from($params['from_email'], $params['from_name']);
                $message->replyTo($params['reply_email'], @$params['reply_name']);

                $message->to($params['to'])->subject($params['subject']);

                if ( !empty($params['attachments']) )
                {
                    if ( !is_array($params['attachments']) )
                    {
                        $params['attachments'] = [ $params['attachments']=>[] ];
                    }

                    foreach ($params['attachments'] as $attachment => $definition)
                    {
                        $message->attach($attachment,$definition);
                    }
                }
            });
        }
        catch (\Exception $e) {
            \Log::error($e, $params);
            $res = false;
        }

		// Restore mail configuration
		if ( $params['backup_required'] )
		{
			\Mail::setSwiftMailer($backup);
		}

		return $res;
	}

	public function getPlanPropertyLimitAttribute()
	{
		$setup = include $this->getSiteSetupPath();

		return @intval( $setup['plan']['max_properties'] );
	}

	public function getPropertyLimitRemainingAttribute()
	{
		// Check if max_properties limit
		$properties_allowed = $this->plan_property_limit;
		if ( $properties_allowed < 1 )
		{
			return 1000;
		}

		// Count current enabled properties
		$properties_current = $this->properties()->where('enabled',1)->count();

		// return difference
		return $properties_allowed - $properties_current;
	}


	public function getMarketplaceHelperAttribute()
	{
		return new \App\Models\Site\MarketplaceHelper($this);
	}

	public function getUpgradePaymentUrlAttribute()
	{
		return implode('/',array_filter([
			rtrim(\Config::get('app.url'),'/'),
			( \LaravelLocalization::getCurrentLocale() == \Config::get('app.locale') ? '' : \LaravelLocalization::getCurrentLocale() ),
			'signup/finish',
			$this->id,
			$this->subdomain,
		]));
	}

	public function getSignupInfo($locale=false)
	{
		$current_locale = \App::getLocale();

		$owner = $this->users->first();
		if ( !$owner )
		{
			return false;
		}

		if ( $locale )
		{
			\App::setLocale($locale);
		}

		$data = [
			'site_url' => $this->main_url,
			'account_url' => $this->account_url,
			'owner_name' => $owner->name,
			'owner_email' => $owner->email,
			'owner_phone' => $owner->phone,
			'pending_request' => $this->planchanges()->with('plan')->pending()->first(),
		];

		\App::setLocale($current_locale);

		return $data;
	}

	public function updatePlan($planchange_id)
	{
		$planchange = $this->planchanges()->withTrashed()->with('plan')->find($planchange_id);
		if ( !$planchange )
		{
			return false;
		}

		$site_update = [
			'plan_id' => $planchange->plan_id,
			'payment_interval' => $planchange->payment_interval,
			'payment_method' => $planchange->payment_method,
			'iban_account' => null,
			'paid_until' => null,
			'invoicing' => $planchange->invoicing,
		];

		switch ( $planchange->payment_method )
		{
			case 'stripe':
				break;
			case 'transfer':
				$site_update['iban_account'] = @$planchange->new_data['iban_account'];
				$site_update['paid_until'] = @$planchange->new_data['paid_until'];
				if ( !$site_update['paid_until'] )
				{
					$site_update['paid_until'] = null;
				}
				break;
		}

		$this->update($site_update);

		// Delete current accepted plan
		$this->planchanges()->where('status','accepted')->where('id','!=',$planchange->id)->delete();

		// Mark current as accepted
		$planchange->status = 'accepted';
		$planchange->save();

		// If new plan is not free
		if ( $planchange->plan && !$planchange->plan->is_free )
		{
			// Enable all languages
			foreach (\App\Models\Locale::where('web',1)->lists('id','locale') as $locale => $locale_id)
			{
				if ( $this->locales->contains( $locale_id ) )
				{
					continue;
				}

				$this->locales()->attach( $locale_id );
			}
		}

		// Send mail to owners
		$locale_backup = \App::getLocale();
		$email_data = $planchange->site->getSignupInfo( $planchange->locale );
		$email_data['pending_request'] = $planchange;
		if ( @$email_data['owner_email'] )
		{
			\App::setLocale( $planchange->locale );
			$html = view('emails.planchange.accept', $email_data)->render();
			\Mail::send('dummy', [ 'content' => $html ], function($message) use ($email_data) {
				$message->from( env('MAIL_FROM_EMAIL'), env('MAIL_FROM_NAME') );
				$message->subject( trans('admin/planchange.accept.subject') );
				$message->to( $email_data['owner_email'] );
			});
		}
		\App::setLocale( $locale_backup );

		return true;
	}

	public function getGroupedPriceranges()
	{
		return (object) [
			'sale' => $this->priceranges->where('type','sale')->sortBy('position'),
			'rent' => $this->priceranges->where('type','rent')->sortBy('position'),
		];
	}

	public function getEnabledCountriesAttribute()
	{
		$query = \App\Models\Geography\Country::withTranslations()->enabled();

		if ( $this->country_ids )
		{
			$query->whereIn('countries.id',$this->country_ids);
		}

		$countries = $query->orderBy('name')->lists('name','id');

		return $countries;
	}

	public static function getTimezoneOptions()
	{
		$timezones = [];

		foreach (\DateTimeZone::listIdentifiers(\DateTimeZone::ALL) as $timezone)
		{
			$timezones[$timezone] = $timezone;
		}

		return $timezones;
	}

	public function preparePaymentData($data)
	{
		$plan_id = empty($data['plan_id']) ? $this->plan_id : $data['plan_id'];

		// Site data
		$payment = [
			'site_id' => $this->id,
			'plan_id' => $plan_id,
			'trigger' => '',
			'paid_from' => null,
			'paid_until' => null,
			'payment_method' =>  '',
			'payment_amount' => 0,
			'payment_currency' => $this->plan->currency,
			'payment_vat' => $this->vat,
			'reseller_id' =>  $this->reseller ? $this->reseller->id : null,
			'reseller_variable' => $this->reseller ? @floatval($this->reseller->plans_commissions[$plan_id]['commission_percentage']) : 0,
			'reseller_fixed' => $this->reseller ? @floatval($this->reseller->plans_commissions[$plan_id]['commission_fixed']) : 0,
			'data' => $data,
		];

		// Provided data
		$payment = array_merge($payment,$data);

		// Reseller payment amount
		$payment_net_amount = $payment['payment_amount'] / ( ( 100 + $payment['payment_vat'] ) / 100 );
		$payment['reseller_amount'] = $payment['reseller_fixed'] + ( $payment_net_amount * $payment['reseller_variable'] / 100 );

		// Exchange rate
		$currency_rate = \App\Models\CurrencyConverter::convert(1, $payment['payment_currency'], 'EUR');
		$payment['payment_rate'] = $currency_rate;
		$payment['reseller_rate'] = $currency_rate;

		return $payment;
	}

	public function getCanHideMolistaAttribute()
	{
		if ( @$this->plan->level >= 2 )
		{
			return true;
		}

		return false;
	}

	public function importTicketingCustomers()
	{
		// Get ticket customers
		$contacts = $this->ticket_adm->getContacts();

		if ( !$contacts )
		{
			return false;
		}

		// Get current customers
		$contact_ids = $this->customers()->where('ticket_contact_id', '!=','')->lists('ticket_contact_id')->all();

		foreach ($contacts as $contact)
		{
			if ( in_array($contact->id, $contact_ids) )
			{
				continue;
			}

			$customer = $this->customers()->where('email', $contact->email)->first();
			if ( $customer )
			{
				if ( !$customer->ticket_contact_id )
				{
					$customer->update([
						'ticket_contact_id' => $contact->id,
					]);
				}
			}
			else
			{
				$fullname_parts = explode(' ', $contact->fullname, 2);
				$first_name = empty($fullname_parts[0]) ? '' : $fullname_parts[0];
				$last_name = empty($fullname_parts[1]) ? '' : $fullname_parts[1];

				$this->customers()->create([
					'first_name' => $first_name,
					'last_name' => $last_name,
					'email' => $contact->email,
					'phone' => $contact->phone ? $contact->phone : '',
					'locale' => config()->get('app.fallback_locale'),
					'origin' => $contact->referer  ? $contact->referer : 'tickets',
					'ticket_contact_id' => $contact->id,
				]);
			}
		}

		return true;
	}

}
