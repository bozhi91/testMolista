<?php

namespace App;

use \App\TranslatableModel;

use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends TranslatableModel
{
    use SoftDeletes;

	public $translatedAttributes = [ 'title', 'description', 'slug' ];

	protected $guarded = [];

    protected $dates = ['deleted_at'];

	public static function boot()
	{
		parent::boot();

		// Whenever a property is created
		static::created(function($property){
			$site = $property->site;

			// Add site owners as property owners
			foreach ($site->users as $user)
			{
				// If company
				if ( $user->hasRole('company') )
				{
					// Attach as owner
					$property->users()->attach($user->id, [
						'is_owner' => 1,
					]);
				}
			}
		});

	}

	public function site()
	{
		return $this->belongsTo('App\Site');
	}

	public function users() {
		return $this->belongsToMany('App\User', 'properties_users', 'property_id', 'user_id');
	}
	public function city()
	{
		return $this->belongsTo('App\Models\Geography\City');
	}

	public function state()
	{
		return $this->belongsTo('App\Models\Geography\State');
	}

	public function images() {
		return $this->hasMany('App\Models\Property\Images');
	}

	public function services() {
		return $this->belongsToMany('App\Models\Property\Service', 'properties_services', 'property_id', 'service_id');
	}

	public function hasService($id)
	{
		$services = $this->services;

		if ( count($services) < 1 )
		{
			return false;
		}

		foreach ($services as $service)
		{
			// check locale (iso)
			if ( $service->id == $id )
			{
				return true;
			}
		}

		return false;
	}

	public function getFullUrlAttribute()
	{
		$site_url = rtrim($this->site->main_url, '/');

		$property_url = str_replace(
							\Config::get('app.application_url'), 
							'', 
							action('Web\PropertiesController@details', $this->slug)
						);

		return implode('/', [
			$site_url,
			$property_url,
		]);
	}

	public function scopeEnabled($query)
	{
		return $query->where('enabled', 1);
	}

	public function scopeOfSite($query, $site_id)
	{
		return $query->where('site_id', $site_id);
	}

	static public function getModes() {
		return [
			'sale', 
			'rent', 
		];
	}
	static public function getModeOptions() {
		$options = [];

		foreach (self::getModes() as $key)
		{
			$options[$key] = trans("web/properties.mode.{$key}");
		}

		return $options;
	}
	static public function getModeOptionsAdmin() {
		$options = [];

		foreach (self::getModes() as $key)
		{
			$options[$key] = trans("admin/properties.mode.{$key}");
		}

		return $options;
	}

	static public function getTypeOptions() {
		return [
			'house' => trans('web/properties.type.house'), 
			'apartment' => trans('web/properties.type.apartment'), 
			'duplex' => trans('web/properties.type.duplex'), 
			'penthouse' => trans('web/properties.type.penthouse'), 
			'villa' => trans('web/properties.type.villa'), 
		];
	}

	static public function getCurrencyOptions() {
		return [
			'EUR' => [
				'currency_iso' => 'EUR',
				'currency_symbol' => '€',
				'currency_decimals' => 2,
				'currency_position' => 'after',
			],
		];
	}

	static public function getSizeUnitOptions() {
		// [TODO] Integrate with conversion plugin
		return [
			'sqm' => [
				'iso' => 'sqm',
				'symbol' => 'm²',
			],
		];
	}

}
