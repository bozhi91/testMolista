<?php

namespace App;

use \App\TranslatableModel;

use Swift_Mailer;

class Site extends TranslatableModel
{
	public $translatedAttributes = ['title','subtitle','description'];

	protected $table = 'sites';
	protected $guarded = [];

	public static function boot()
	{
		parent::boot();

		// Whenever a site is updated
		static::updated(function($site){
			$site->ticket_adm->updateSite();
		});
	}

	public function stats() {
		return $this->hasMany('App\Models\Site\Stats');
	}

	public function users() {
		return $this->belongsToMany('App\User', 'sites_users', 'site_id', 'user_id');
	}
	public function getUsersIdsAttribute() {
		$users = [];

		foreach ($this->users as $user)
		{
			$users[] = $user->id;
		}

		return $users;
	}
	public function getOwnersIdsAttribute() {
		return \App\User::withRole('company')->whereIn('id', $this->users_ids)->lists('id')->toArray();
	}
	public function getEmployeesIdsAttribute() {
		return \App\User::withRole('employee')->whereIn('id', $this->users_ids)->lists('id')->toArray();
	}

	public function customers() {
		return $this->hasMany('App\Models\Site\Customer');
	}

	public function properties() {
		return $this->hasMany('App\Property');
	}

	public function api_keys() {
		return $this->hasMany('App\Models\ApiKey');
	}

	public function menus() {
		return $this->hasMany('App\Models\Site\Menu');
	}

	public function widgets() {
		return $this->hasMany('App\Models\Site\Widget');
	}

	public function pages() {
		return $this->hasMany('App\Models\Site\Page');
	}

	public function social() {
		return $this->hasMany('App\SiteSocial');
	}
	public function getSocialArrayAttribute() {
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

	public function domains() {
		return $this->hasMany('App\SiteDomains');
	}
	public function getDomainsArrayAttribute() {
		$domains = [];

		foreach ($this->domains as $domain)
		{
			$domains[$domain->id] = $domain->domain;
		}

		return $domains;
	}

	public function locales() {
		return $this->belongsToMany('App\Models\Locale', 'sites_locales', 'site_id', 'locale_id');
	}
	public function getLocalesArrayAttribute() {
		$locales = [];

		foreach ($this->locales as $locale)
		{
			$locales[] = $locale->locale;
		}

		return $locales;
	}

	public function getMainUrlAttribute()
	{
		if ( count($this->domains) < 1 )
		{
			return \Config::get('app.application_protocol') . "://{$this->subdomain}." . \Config::get('app.application_domain');
		}

		return $this->domains->sortByDesc('default')->first()->domain;
	}

	public function getTicketingEnabledAttribute()
	{
		if ( $this->ticket_site_id && $this->ticket_owner_token && $this->mailer_service == 'custom' )
		{
			return true;
		}

		return false;
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
		$owner->autologin_token = sha1( $this->id . uniqid() );
		while ( \App\User::where('autologin_token', $owner->autologin_token)->count() > 0 )
		{
			$owner->autologin_token = sha1( $this->id . uniqid() . rand(1000, 9999) );
		}
		$owner->save();

		// Return autologin url
		return str_replace(
							rtrim(\Config::get('app.application_url'),'/'), //replaces current url
							rtrim($this->main_url,'/'), // with website url
							action('Auth\AuthController@autologin', [ $owner->id, $owner->autologin_token ])
						);
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
	public function getMailerServiceAttribute() {
		return @$this->mailer['service'];
	}
	public function getMailerOutAttribute() {
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
	public function getMailerInAttribute() {
		$protocol = @$this->mailer['in']['protocol'];

		if ( !$protocol || $this->mailer_service != 'custom' )
		{
			return false;
		}

		return @$this->mailer['in'];
	}

	public function getTicketAdmAttribute() {
		return new \App\Models\Site\TicketAdm( $this->id );
	}

	public function scopeEnabled($query)
	{
		return $query->where("{$this->getTable()}.enabled", 1);
	}

	public function scopeCurrent($query)
	{
		// Parse url
		$parts = parse_url( url()->current() );

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
				return new Swift_Mailer($transport);
			default:
				return \Mail::getSwiftMailer();
		}
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
			case 'mail':
				break;
			default:
				\Log::error("Mailer service '{$params['service']}' is not valid for site ID {$this->id}");
				return false;
		}

		// Send email
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

		// Restore mail configuration
		if ( $params['backup_required'] )
		{
			\Mail::setSwiftMailer($backup);
		}

		return $res;
	}

}
