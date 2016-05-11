<?php

namespace App;

use \App\TranslatableModel;

use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends TranslatableModel
{
    use SoftDeletes;

	public $translatedAttributes = [ 'title', 'description', 'slug', 'label' ];

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

		// Whenever a property is saved
		static::saved(function($property){
			\File::deleteDirectory(public_path($property->pdf_folder), true);
		});
	}

	public function site()
	{
		return $this->belongsTo('App\Site');
	}

	public function users() {
		return $this->belongsToMany('App\User', 'properties_users', 'property_id', 'user_id')->withPivot('is_owner');
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

	public function getPdfFolderAttribute()
	{
		return "sites/{$this->site_id}/properties/{$this->id}/pdf";
	}
	public function getPdfFile($locale) 
	{
		// Check directory
		$dir = public_path($this->pdf_folder);
		if ( !is_dir($dir) )
		{
			\File::makeDirectory($dir);
		}

		// Check PDF
		$filepath = "{$dir}/property-{$locale}.pdf";
		if ( !file_exists($filepath) )
		{
			$locale_backup = \LaravelLocalization::getCurrentLocale();
			\LaravelLocalization::setLocale($locale);
			\PDF::loadView('pdf/property', [
				'property' => $this
			])->save($filepath);
			\LaravelLocalization::setLocale($locale_backup);
		}

		return $filepath;
	}

	public function getImageFolderAttribute()
	{
		return asset("sites/{$this->site_id}/properties/{$this->id}");
	}
	public function getImagePathAttribute()
	{
		return public_path("sites/{$this->site_id}/properties/{$this->id}");
	}
	public function getMainImageAttribute()
	{
		foreach ($this->images->sortByDesc('default') as $image)
		{
			return "{$this->image_folder}/{$image->image}";
		}

		return false;
	}

	// [TODO]
	public function getRelatedPropertiesAttribute()
	{
		return \App\Property::enabled()
			->ofSite($this->site_id)
			->where('properties.id','!=',$this->id)
			->with('images')
			->with('state')
			->with('city')
			->withTranslations()
			->orderByRaw("RAND()")
			->limit(3)
			->get();
	}

	public function getFullUrlAttribute()
	{
		$site_url = rtrim($this->site->main_url, '/');
		$property_url = action('Web\PropertiesController@details', $this->slug);

		// Is domain right?
		if ( preg_match('#^'.$site_url.'#', $property_url) )
		{
			return $property_url;
		}

		// Fix wrong domain
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

	public function getContactsAttribute()
	{
		$contacts = $this->users;

		if ( $this->users->count() < 1 )
		{
			return [];
		}

		$owners = [];
		$managers = [];
		foreach ($this->users as $contact)
		{
			if ( $contact->pivot->is_owner )
			{
				$owners[$contact->email] = $contact;
			}
			else
			{
				$managers[$contact->email] = $contact;
			}
		}

		return empty($managers) ? $owners : $managers;
	}

	public function scopeEnabled($query)
	{
		return $query->where('properties.enabled', 1);
	}

	public function scopeHighlighted($query)
	{
		return $query->where('properties.highlighted', 1);
	}

	public function scopeOfSite($query, $site_id)
	{
		return $query->where('properties.site_id', $site_id);
	}

	public function scopeInState($query, $state_id)
	{
		if ( !is_int($state_id) )
		{
			$state_id = \App\Models\Geography\State::where('slug', $state_id)->value('id');
		}

		return $query->where('properties.state_id', $state_id);
	}

	public function scopeInCity($query, $city_id)
	{
		if ( !is_int($city_id) )
		{
			$city_id = \App\Models\Geography\City::where('slug', $city_id)->value('id');
		}

		return $query->where('properties.city_id', $city_id);
	}

	public function scopeWithRange($query, $field, $range)
	{
		$limits = explode('-', $range);
		if ( count($limits) != 2 ) 
		{
			return $query->whereRaw('1=2');
		}

		$min = floatval($limits[0]);
		$max = floatval($limits[1]);

		if ($min)
		{
			$query->where("properties.{$field}", '>', $min);
		}

		if ($max)
		{
			$query->where("properties.{$field}", '<=', $max);
		}

		return $query;
	}

	public function scopeWithServices($query, $services)
	{
		if ( !is_array($services) ) 
		{
			$services = [ $services ];
		}

		// Get services ids
		$service_ids = \App\Models\Property\ServiceTranslation::whereIn('slug',$services)->lists('service_id')->toArray();
		if ( empty($service_ids) )
		{
			return $query->whereRaw('1=2');
		}

		foreach ($service_ids as $service_id) 
		{
			$query->whereHas('services', function ($query) use ($service_id) {
				$query->where('services.id', $service_id);
			});
		}

		return $query;
	}

	static public function getModes() 
	{
		return [
			'sale', 
			'rent', 
		];
	}
	static public function getModeOptions() 
	{
		$options = [];

		foreach (self::getModes() as $key)
		{
			$options[$key] = trans("web/properties.mode.{$key}");
		}

		return $options;
	}
	static public function getModeOptionsAdmin() 
	{
		$options = [];

		foreach (self::getModes() as $key)
		{
			$options[$key] = trans("admin/properties.mode.{$key}");
		}

		return $options;
	}

	static public function getTypeOptions() 
	{
		return [
			'house' => trans('web/properties.type.house'), 
			'apartment' => trans('web/properties.type.apartment'), 
			'duplex' => trans('web/properties.type.duplex'), 
			'penthouse' => trans('web/properties.type.penthouse'), 
			'villa' => trans('web/properties.type.villa'), 
			'store' => trans('web/properties.type.store'), 
			'lot' => trans('web/properties.type.lot'), 
		];
	}

	static public function getPriceOptions() 
	{
		return [
			'less-100000' => '< 100.000',
			'100000-250000' => '100.001 - 250.000',
			'250000-500000' => '250.001 - 500.000',
			'500000-1000000' => '500.001 - 1.000.000',
			'1000000-more' => '> 1.000.000',
		];
	}

	static public function getSizeOptions() 
	{
		return [
			'less-100' => '< 100 m²',
			'100-250' => '101 - 250 m²',
			'250-500' => '251 - 500 m²',
			'500-1000' => '501 - 1.000 m²',
			'1000-more' => '> 1.000 m²',
		];
	}

	static public function getRoomOptions() 
	{
		return [
			'0-2' => '1 - 2',
			'2-5' => '3 - 5',
			'5-10' => '6 - 10',
			'10-more' => '> 10',
		];
	}

	static public function getBathOptions() 
	{
		return [
			'0-2' => '1 - 2',
			'2-5' => '3- 5',
			'5-more' => '> 5',
		];
	}

	static public function getCurrencyOptions() 
	{
		return [
			'EUR' => [
				'currency_iso' => 'EUR',
				'currency_symbol' => '€',
				'currency_decimals' => 2,
				'currency_position' => 'after',
			],
		];
	}

	static public function getSizeUnitOptions() 
	{
		return [
			'sqm' => [
				'iso' => 'sqm',
				'symbol' => 'm²',
			],
		];
	}

	static public function getEcOptions() 
	{
		return [
			'A' => 'A',
			'B' => 'B',
			'C' => 'C',
			'D' => 'D',
			'E' => 'E',
			'F' => 'F',
			'G' => 'G',
		];
	}

}
