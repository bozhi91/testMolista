<?php

namespace App;

use \App\TranslatableModel;

class Site extends TranslatableModel
{
	public $translatedAttributes = ['title','subtitle','description'];

	protected $table = 'sites';
	protected $guarded = [];

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

	public function properties() {
		return $this->hasMany('App\Property');
	}

	public function api_keys() {
		return $this->hasMany('App\Models\ApiKey');
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

}
