<?php

namespace App;

use \App\TranslatableModel;
use OwenIt\Auditing\AuditingTrait;

use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends TranslatableModel
{
	use SoftDeletes;

	use AuditingTrait;
	protected $dontKeepLogOf = [
		'site_id',
		'label_color',
		'publisher_id', 
		'published_at', 
		'created_at', 
		'updated_at',
	];
	protected $auditableTypes = [ 'created', 'saved', 'deleted' ];
	public static $logCustomMessage = '{user.name|Anonymous} {type} this property {elapsed_time}';
	public static $logCustomFields = [];

	public $translatedAttributes = [ 'title', 'description', 'slug', 'label' ];


	protected $guarded = [];

	protected $dates = ['deleted_at'];

    protected $casts = [
        'address_parts' => 'array',
    ];

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
			// Delete cached PDF files
			\File::deleteDirectory(public_path($property->pdf_folder), true);
			// Create / Update on ticket system
			if ( $property->ref )
			{
				$property->site->ticket_adm->associateItem($property);
			}
		});

		static::$logCustomFields = [
			'ref'  => trans('account/properties.ref'),
			'type'  => trans('account/properties.type'),
			'mode'  => trans('account/properties.mode'),
			'price'  => trans('account/properties.price'),
			'currency'  => trans('account/properties.currency'),
			'size'  => trans('account/properties.size'),
			'size_unit'  => trans('account/properties.size_unit'),
			'rooms'  => trans('account/properties.rooms'),
			'baths'  => trans('account/properties.baths'),
			'newly_build'  => trans('account/properties.newly_build'),
			'second_hand'  => trans('account/properties.second_hand'),
			'highlighted'  => trans('account/properties.highlighted'),
			'enabled'  => trans('account/properties.enabled'),
			'ec'  => trans('account/properties.energy.certificate'),
			'ec_pending'  => trans('account/properties.energy.certificate.pending.full'),
			'country_id' => trans('account/properties.country'),
			'territory_id' => trans('account/properties.territory'),
			'state_id' => trans('account/properties.state'),
			'city_id' => trans('account/properties.city'),
			'district' => trans('account/properties.district'),
			'address' => trans('account/properties.address'),
			'zipcode' => trans('account/properties.zipcode'),
			'show_address' => trans('account/properties.show_address'),
			'lat' => trans('account/properties.lat'),
			'lng' => trans('account/properties.lng'),
		];
	}

	public function site()
	{
		return $this->belongsTo('App\Site');
	}

	public function users() {
		return $this->belongsToMany('App\User', 'properties_users', 'property_id', 'user_id')->withPivot('is_owner');
	}

	public function customers() {
		return $this->belongsToMany('App\Models\Site\Customer', 'properties_customers', 'property_id', 'customer_id');
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

	public function catches() {
		return $this->hasMany('App\Models\Property\Catches');
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

	public function getLocationArrayAttribute()
	{
		return array_filter([
			'district' => @$this->district,
			'city' => @$this->city->name,
			'state' => @$this->state->name,
		]);
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
			\File::makeDirectory($dir, 0777, true, true);
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

	public function getCatchCurrentAttribute()
	{
		return $this->catches()->where('status','active')->with('employee')->orderBy('catch_date','desc')->first();
	}

	public function getCatchTransactionsAttribute()
	{
		return $this->catches()->where('status','!=','active')->with('employee')->with('buyer')->orderBy('transaction_date','desc')->get();
	}

	public function getCatchKpisAttribute()
	{
		if ( $this->catch_current )
		{
			return false;
		}

		return $this->catches()->whereIn('status', [ 'sold', 'rent' ])->with('employee')->with('buyer')->with('seller')->orderBy('transaction_date','desc')->first();
	}


	public function getRelatedPropertiesAttribute()
	{
		$limit = 3;

		$ids = \App\Property::withBasicRelationship($this)
					->where('properties.city_id', $this->city_id) 
					->where('properties.type', $this->type)
					->orderBy('properties.price','desc')
					->orderByRaw('RAND()')
					->limit($limit)->lists('id')->all();

		$limit -= count($ids);
		if ( $limit < 1 )
		{
			return $this->_related_properties($ids);
		}

		// Same state and type
		$_ids = \App\Property::withBasicRelationship($this)
					->whereNotIn('properties.id', $ids)
					->where('properties.state_id', $this->state_id)
					->where('properties.type', $this->type)
					->orderBy('properties.price','desc')
					->orderByRaw('RAND()')
					->limit($limit)->lists('id')->all();

		$limit -= count($_ids);
		$ids = array_merge($_ids, $ids);
		if ( $limit < 1 )
		{
			return $this->_related_properties($ids);
		}

		// Same state
		$_ids = \App\Property::withBasicRelationship($this)
					->whereNotIn('properties.id', $ids)
					->where('properties.state_id', $this->state_id)
					->orderBy('properties.price','desc')
					->orderByRaw('RAND()')
					->limit($limit)->lists('id')->all();

		$limit -= count($_ids);
		$ids = array_merge($_ids, $ids);
		if ( $limit < 1 )
		{
			return $this->_related_properties($ids);
		}

		// Whatever...
		$_ids = \App\Property::withBasicRelationship($this)
					->whereNotIn('properties.id', $ids)
					->orderByRaw('RAND()')
					->limit($limit)->lists('id')->all();
		$ids = array_merge($_ids, $ids);
		return $this->_related_properties($ids);
	}
	public function _related_properties($ids)
	{
		return \App\Property::withEverything()
			->whereIn('properties.id',$ids)
			->orderByRaw('RAND()')
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

	public function getUniqueManagerAttribute()
	{
		$managers = $this->users()->withRole('employee');

		if ( $managers->count() !== 1 )
		{
			return false;
		}

		return $managers->first();
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

		$min = @floatval($limits[0]);
		$max = @floatval($limits[1]);

		if ($min)
		{
			$query->where("properties.{$field}", '>=', $min);
		}

		if ($max)
		{
			$query->where("properties.{$field}", '<=', $max);
		}

		return $query;
	}

	// [TODO] Standardize currency
	public function scopeWithPriceBetween($query, $range, $currency)
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
			$query->where('properties.price', '>=', $min);
		}

		if ($max)
		{
			$query->where('properties.price', '<=', $max);
		}

		return $query;
	}

	// [TODO] Standardize size unit
	public function scopeWithSizeBetween($query, $range, $size_unit)
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
			$query->where('properties.size', '>=', $min);
		}

		if ($max)
		{
			$query->where('properties.size', '<=', $max);
		}

		return $query;
	}

	public function scopeWithServices($query, $services)
	{
		if ( !$services )
		{
			return $query;
		}

		if ( !is_array($services) ) 
		{
			$services = [ $services ];
		}

		// Get services ids
		$service_ids = \App\Models\Property\ServiceTranslation::whereIn('slug',$services)->lists('service_id')->all();
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

	public function scopeWithBasicRelationship($query,$property)
	{
		return $query
				->enabled()
				->ofSite($property->site_id)
				->where('properties.id','!=',$property->id)
				->withPriceBetween("0-{$property->price}", $property->currency)
				->where('properties.mode', $property->mode)
				->where('properties.country_id', $property->country_id);
	}

	public function scopeWithEverything($query)
	{
		return $query
				->withTranslations()
				->with('images')
				->with('state')
				->with('city');
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

	static public function getPriceOptions($site_id = false) 
	{
		$steps = 5;

		$defaults = [
			'rent' => [
				'less-750' => '< 750€',
				'750-1000' => '751€ - 1.000€',
				'1000-1250' => '1.001€ - 1.250€',
				'1250-1500' => '1.251€ - 1.500€',
				'1500-more' => '> 1.500€',
			],
			'sale' => [
				'less-100000' => '< 100.000€',
				'100000-250000' => '100.001€ - 250.000€',
				'250000-500000' => '250.001€ - 500.000€',
				'500000-1000000' => '500.001€ - 1.000.000€',
				'1000000-more' => '> 1.000.000€',
			],
		];

		$limits = self::selectRaw("properties.`mode` as mode, MIN(properties.`price`) as min, MAX(properties.`price`) as max")
							->whereIn('properties.mode', array_keys($defaults))
							->ofSite($site_id)
							->enabled()
							->groupBy('properties.mode')
							->get();

		if ( $limits->count() < 1 )
		{
			return $defaults;
		}

		foreach ($limits as $limit)
		{
			switch ( $limit->mode )
			{
				case 'rent':
					$diff_min = 100;
					break;
				case 'sale':
					$diff_min = 10000;
					break;
			}

			$min = floor($limit->min / $diff_min) * $diff_min;
			$max = ceil($limit->max / $diff_min) * $diff_min;
			$diff = ($max - $min) / ($steps + 1);

			if ( $diff < $diff_min )
			{
				continue;
			}

			$defaults[$limit->mode] = [];

			$diff = ceil($diff / $diff_min) * $diff_min;

			for ($i=1; $i<=$steps; $i++)
			{
				$current = $min + ( $diff * $i );

				if ( $i == 1 )
				{
					$defaults[$limit->mode]["less-{$current}"] = "< ".number_format($current,0,',','.')."€";
				}
				elseif ($i == $steps )
				{
					$defaults[$limit->mode]["{$current}-more"] = "> ".number_format($current,0,',','.')."€";
				}
				else
				{
					$defaults[$limit->mode]["{$last}-{$current}"] = number_format($last+1,0,',','.')."€ - ".number_format($current,0,',','.')."€";
				}

				$last = $current;
			}
		}

		return $defaults;
	}

	static public function getSizeOptions() 
	{
		return [
			'less-100' => '< 100 m²',
			'100-250' => '100 - 250 m²',
			'250-500' => '250 - 500 m²',
			'500-1000' => '500 - 1.000 m²',
			'1000-more' => '> 1.000 m²',
		];
	}

	static public function getRoomOptions() 
	{
		return [
			'0-2' => '1 - 2',
			'3-5' => '3 - 5',
			'6-10' => '6 - 10',
			'11-more' => '> 10',
		];
	}

	static public function getBathOptions() 
	{
		return [
			'0-2' => '1 - 2',
			'3-5' => '3- 5',
			'6-more' => '> 5',
		];
	}

	static public function getCurrencyOptions() 
	{
		return [
			'EUR' => [
				'iso' => 'EUR',
				'symbol' => '€',
				'decimals' => 0,
				'position' => 'after',
			],
		];
	}
	static public function getCurrencyOption($iso) 
	{
		$options = static::getCurrencyOptions();
		return ( $iso && array_key_exists($iso, $options) ) ? $options[$iso] : false;
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
	static public function getSizeUnitOption($iso) 
	{
		$options = static::getSizeUnitOptions();
		return ( $iso && array_key_exists($iso, $options) ) ? $options[$iso] : false;
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

	static public function getSortOptions() 
	{
		return [
			// field-sense => title
			'price-asc' => trans('web/search.price.asc'),
			'price-desc' => trans('web/search.price.desc'),
			'title-asc' => trans('web/search.title.asc'),
			'title-desc' => trans('web/search.title.desc'),
		];
	}

}
