<?php

namespace App;

use \App\TranslatableModel;
use OwenIt\Auditing\AuditingTrait;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Property extends TranslatableModel
{
	use SoftDeletes;

	use AuditingTrait;
	protected $dontKeepLogOf = [
		'site_id',
		'label_color',
		'currency',
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
		'details' => 'array',
		'marketplace_attributes' => 'array',
	];

	protected $marketplace_info;

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
			// Delete cached files PDF, QRs, site XMLs
			\File::deleteDirectory(public_path($property->pdf_folder), true);
			\File::deleteDirectory(public_path($property->qr_folder), true);
			\File::deleteDirectory($property->site->xml_path, true);
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
			'bank_owned'  => trans('account/properties.bank_owned'),
			'private_owned'  => trans('account/properties.private_owned'),
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

	public function calendars() {
		return $this->belongsToMany('App\Models\Calendar', 'calendars_properties', 'property_id', 'calendar_id');
	}

	public function customers() {
		return $this->belongsToMany('App\Models\Site\Customer', 'properties_customers', 'property_id', 'customer_id');
	}

	public function country()
	{
		return $this->belongsTo('App\Models\Geography\Country');
	}

	public function territory()
	{
		return $this->belongsTo('App\Models\Geography\Territory');
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

	public function videos() {
		return $this->hasMany('App\Models\Property\Videos');
	}

	public function catches() {
		return $this->hasMany('App\Models\Property\Catches');
	}

	public function documents() {
		return $this->hasMany('App\Models\Property\Documents');
	}

	public function services() {
		return $this->belongsToMany('App\Models\Property\Service', 'properties_services', 'property_id', 'service_id')->withTranslations();
	}

	/* Retrieve all the property media */
	public function media()
	{
		$images = $this->images->sortBy('position')->values();
		$media = $this->videos->sortBy('position_video')->values();

		if (!$images->count()) return $media;

		// Put videos on second place:
		// first image...
		$media->prepend($images->shift());
		// and then the rest at the end
		$media = $media->merge($images);

		return $media;
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

	public function marketplaces()
	{
		return $this->belongsToMany('App\Models\Marketplace', 'properties_marketplaces', 'property_id', 'marketplace_id');
	}
	public function getMarketplacesIdsAttribute() {
		$marketplaces = [];

		foreach ($this->marketplaces as $marketplace)
		{
			$marketplaces[$marketplace->id] = $marketplace->id;
		}

		return $marketplaces;
	}

	public function infocurrency()
	{
		return $this->hasOne('App\Models\Currency', 'code', 'currency')->withTranslations();
	}

	public function getLocationArrayAttribute()
	{
		return array_filter([
			'district' => @$this->district,
			'city' => @$this->city->name,
			'state' => @$this->state->name,
		]);
	}

	public function getFullAddressAttribute()
	{
		return implode(', ', array_filter([
			@$this->address,
			@$this->district,
			@$this->city->name,
			@$this->state->name,
		]));
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

		$last_time = strtotime("2016-09-07");
		if ( strtotime($this->site->updated_at) > $last_time )
		{
			$last_time = strtotime($this->site->updated_at);
		}

		if ( !file_exists($filepath) || filemtime($filepath) < $last_time )
		{
			$locale_backup = \LaravelLocalization::getCurrentLocale();
			\LaravelLocalization::setLocale($locale);

			$main_image = false;
			$other_images = [];

			if ( $this->images->count() > 0 )
			{
				foreach ($this->images->sortBy('position') as $image)
				{
					if ( !$main_image )
					{
						$main_image = $this->getPdfFileImageTmp("{$this->image_path}/{$image->image}", 525, 215);
					}
					elseif ( count($other_images) < 2 )
					{
						$other_images[] = $this->getPdfFileImageTmp("{$this->image_path}/{$image->image}", 255, 160);
					}
					else
					{
						break;
					}
				}
			}

			// Get css
			if ( $this->site->theme && file_exists( public_path("themes/{$this->site->theme}/compiled/css/pdf.css") ) )
			{
				$css = \File::get( public_path("themes/{$this->site->theme}/compiled/css/pdf.css") );
			}
			elseif ( file_exists( public_path("compiled/css/pdf.css") ) )
			{
				$css = \File::get( public_path("compiled/css/pdf.css") );
			}
			else
			{
				$css = false;
			}

			//Get the site
            $prop = DB::table("sites")
                ->select("plan_id")
                ->where('id',session("SiteSetup")['site_id'])
                ->first();


			\PDF::loadView('pdf.property', [
				'css' => $css,
				'property' => $this,
				'main_image' => $main_image,
				'other_images' => $other_images,
                'plan'=> $prop->plan_id,
			])->save($filepath);

			// Delete tmp images
			if ( $main_image )
			{
				unlink($main_image);
			}
			foreach ($other_images as $other_image)
			{
				unlink($other_image);
			}

			\LaravelLocalization::setLocale($locale_backup);
		}

		return $filepath;
	}

	public function getPdfFileImageTmp($image,$width,$height)
	{
		$folder = public_path('_temp');
		if ( !is_dir($folder) )
		{
			\File::makeDirectory(public_path('_temp'), 0775, true);
		}

		$filename = uniqid('pdf_image_', true);
		$destination = "{$folder}/{$filename}.jpg";

		while ( file_exists($destination) )
		{
			$filename = uniqid('pdf', true);
			$destination = "{$folder}/{$filename}.jpg";
		}

		// Create thumb
		$thumb = \Image::make($image);

		$thumb->encode('jpg');

		$thumb->fit($width, $height, function($constraint) {
			$constraint->aspectRatio();
		})->save($destination);

		return $destination;
	}

	public function getLatPublicAttribute()
	{
		if ( $this->show_address )
		{
			return $this->lat;
		}

		return $this->lat + (((rand(0,99)/100) - 0.5) * 0.005);
	}

	public function getLngPublicAttribute()
	{
		if ( $this->show_address )
		{
			return $this->lng;
		}

		return $this->lng + (((rand(0,99)/100) - 0.5) * 0.005);
	}

	public function getQrFolderAttribute()
	{
		return "sites/{$this->site_id}/properties/{$this->id}/qr";
	}
	public function getQrFile($locale)
	{
		// Check directory
		$dir = public_path($this->qr_folder);
		if ( !is_dir($dir) )
		{
			\File::makeDirectory($dir, 0777, true, true);
		}

		// Check PDF
		$filepath = "{$dir}/property-{$locale}.png";
		if ( true || !file_exists($filepath) )
		{
			$locale_backup = \LaravelLocalization::getCurrentLocale();
			\LaravelLocalization::setLocale($locale);

			\QrCode::encoding('UTF-8')
						->format('png')
						->size(85)
						->margin(0)
						->errorCorrection('H')
						->generate($this->full_url, $filepath);

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
		$dirpath = public_path("sites/{$this->site_id}/properties/{$this->id}");

		if ( !is_dir($dirpath))
		{
			\File::makeDirectory($dirpath, 0777, true, true);
		}

		return $dirpath;
	}

	public function getVideoPathAttribute(){
		$dirpath = public_path("sites/{$this->site_id}/properties/{$this->id}/video");

		if ( !is_dir($dirpath))
		{
			\File::makeDirectory($dirpath, 0777, true, true);
		}

		return $dirpath;
	}

	public function getMainImageAttribute()
	{
		foreach ($this->images->sortByDesc('default') as $image)
		{
			return $image->image_url;
		}

		return false;
	}
	public function getMainImageThumbAttribute()
	{
		if ( !$this->main_image )
		{
			return false;
		}

		$tmp = pathinfo($this->main_image);

		return implode('/', [
					$tmp['dirname'],
					'thumbnail',
					$tmp['basename'],
				]);
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
		return \App\Property::withTranslations()
			->withEverything()
			->whereIn('properties.id',$ids)
			->orderByRaw('RAND()')
			->get();

	}

	public function getFullUrlAttribute()
	{
		$site_url = rtrim($this->site->main_url, '/');
		$property_url = action('Web\PropertiesController@details', [ $this->slug, $this->id ]);

		// Use always the main domain
		$parts = parse_url($property_url);

		$url = $site_url;

		if (!empty($parts['path'])) {
			$url .= $parts['path'];
		}

		if (!empty($parts['query'])) {
			$url .= '?'.$parts['query'];
		}

		if (!empty($parts['fragment'])) {
			$url .= '#'.$parts['fragment'];
		}

		return $url;
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

	public function getMarketplaceInfoAttribute()
	{
		$current_locale = \App::getLocale();
		$fallback_locale = fallback_lang();
		$site_locales = $this->site->locales_array;

		$this->marketplace_info = [
			'id' => $this->id,
			'site_id' => $this->site->id,
			'reference' => $this->ref,
			'title' => [],
			'description' => [],
			'type' => $this->type,
			'mode' => $this->mode,
			'price' => $this->price,
			'currency' => $this->currency,
			'size' => $this->size,
			'size_unit' => $this->size_unit,
			'rooms' => $this->rooms,
			'baths' => $this->baths,
			'ec' => $this->ec,
			'ec_pending' => $this->ec_pending,
			'construction_year' => $this->construction_year,
			'newly_build' => $this->newly_build,
			'second_hand' => $this->second_hand,
			'bank_owned' => $this->bank_owned,
			'private_owned' => $this->private_owned,
			'url' => [],
			'location' => [
				'country' => @$this->country->code,
				'territory' => @$this->territory->name,
				'state' => @$this->state->name,
				'district' => $this->district,
				'city' => @$this->city->name,
				'address' => $this->address,
				'address_parts' => $this->address_parts,
				'zipcode' => $this->zipcode,
				'lat' => $this->lat,
				'lng' => $this->lng,
				'show_address' => $this->show_address
			],
			'images' => [],
			'features' => [],
			'marketplace_attributes' => $this->marketplace_attributes,
			'created_at' => $this->created_at->format('Y-m-d H:i:s'),
			'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
		];

		// Translatable
		$i18n = $this->i18n;
		foreach ([ 'title', 'description' ] as $field)
		{
			foreach ($site_locales as $locale)
			{
				$value = @$i18n[$field][$locale];
				if ( !$value )
				{
					$value = @$i18n[$field][$fallback_locale];
				}
				$this->marketplace_info[$field][$locale] = $value;
			}
		}

		// Property urls
		foreach ($site_locales as $locale)
		{
			\App::setLocale($locale);
			$slug = empty($i18n['slug'][$locale]) ? $i18n['slug'][$fallback_locale] : $i18n['slug'][$locale];
			$temporal_url = parse_url(\LaravelLocalization::getLocalizedURL($locale, action('Web\PropertiesController@details', [ $slug, $this->id ])));
			$this->marketplace_info['url'][$locale] = $this->site->main_url.@$temporal_url['path'];
		}

		// Images
		foreach ($this->images->sortBy('position') as $image)
		{
			$image_url = $image->image_url;

			// Remove version from image url
			$query = parse_url($image_url, PHP_URL_QUERY);
			if ( $query )
			{
				$image_url = str_replace("?{$query}", '', $image_url);
			}

			$this->marketplace_info['images'][] = $image_url;
		}

		// Features
		foreach ($this->services as $service)
		{
			$tmp = [];
			foreach ($site_locales as $locale)
			{
				$tmp[$locale] = @$service->i18n['title'][$locale];
				if ( !$tmp[$locale] )
				{
					$tmp[$locale] = @$service->i18n['title'][$fallback_locale];
				}
			}
			$this->marketplace_info['features'][$service->code] = $tmp;
		}

		// Details
		if (is_array($this->details)) {
			$this->marketplace_info = array_merge($this->details, $this->marketplace_info);
		}

		\App::setLocale($current_locale);

		return $this->marketplace_info;
	}

	public function scopeInHome($query)
	{
		return $query->where('properties.home_slider', 1);
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

	public function scopeWithMarketplaceEnabled($query, $marketplace_id)
	{
		return $query->leftJoin('properties_marketplaces', function($join) use ($marketplace_id) {
					$join->on('properties.id', '=', 'properties_marketplaces.property_id');
					$join->on('properties_marketplaces.marketplace_id', '=', \DB::raw($marketplace_id));
				})
				->addSelect( \DB::raw('IF(properties_marketplaces.`marketplace_id` IS NULL, properties.`export_to_all`, 1) as exported_to_marketplace') );
	}

	public function scopeOfMarketplace($query, $marketplace_id)
	{
		return $query->where(function($query) use($marketplace_id) {
				$query->whereIn('properties.id', function($query) use ($marketplace_id) {
					$query->select('properties_marketplaces.property_id')
						->from('properties_marketplaces')
						->where('marketplace_id', $marketplace_id);
					})->orWhere('properties.export_to_all',1);
				})->addSelect( \DB::raw('1 as exported_to_marketplace') );
	}

	public function scopeDisabledOnMarketplace($query, $marketplace_id)
	{
		$ids = self::ofMarketplace($marketplace_id)->select('properties.id')->get()->pluck('id')->toArray();
		if (!is_array($ids)) {
			$ids = [];
		}
		return $query->whereNotIn('properties.id', $ids);
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

	public function scopeWithTermLike($query, $term)
	{
			$query->where(function($query) use ($term) {
				$query
					// Title
					->whereTranslationLike('title', "%{$term}%")
					// Ref
					->orWhere('ref', 'like', "%{$term}%")
					// show_address == 1
					->orWhere(function($query) use ($term) {
						$query->where('show_address', 1)
							->where(function($query) use ($term) {
								$query
									// Address
									->where('address', 'like', "%{$term}%")
									// District
									->orWhere('district', 'like', "%{$term}%")
									// Zipcode
									->orWhere('zipcode', 'like', "%{$term}%");
							});
					});
			});

	}

	public function scopeWithEverything($query)
	{
		return $query
				->with('images')
				->with('country')
				->with('territory')
				->with('state')
				->with('city')
				->with('images')
				->with('services')
				;
	}

	static public function getModes()
	{
		return [
			'sale',
            'vacationRental',
            'rent',
            'transfer',
		];
	}
	static public function getModeOptions($site_id=false)
	{
		$options = [];

		foreach (self::getModes() as $key)
		{
			$options[$key] = trans("web/properties.mode.{$key}");
		}

		if ( $site_id )
		{
			$assigned = \App\Property::distinct()->select('mode')->where('site_id',$site_id)->lists('mode')->all();
			foreach ($options as $key => $value)
			{
				if ( !in_array($key, $assigned) )
				{
					unset($options[$key]);
				}
			}
		}

		asort($options);

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

	static public function getTypeOptions($site_id=false)
	{
		$options = [
			'house' => trans('web/properties.type.house'),
			'apartment' => trans('web/properties.type.apartment'),
			'flat' => trans('web/properties.type.flat'),
			'duplex' => trans('web/properties.type.duplex'),
			'penthouse' => trans('web/properties.type.penthouse'),
			'villa' => trans('web/properties.type.villa'),
			'store' => trans('web/properties.type.store'),
			'lot' => trans('web/properties.type.lot'),
			'ranch' => trans('web/properties.type.ranch'),
			'hotel' => trans('web/properties.type.hotel'),
			'aparthotel' => trans('web/properties.type.aparthotel'),
			'chalet' => trans('web/properties.type.chalet'),
			'bungalow' => trans('web/properties.type.bungalow'),
			'building' => trans('web/properties.type.building'),
			'industrial' => trans('web/properties.type.industrial'),
			'state' => trans('web/properties.type.state'),
			'farmhouse' => trans('web/properties.type.farmhouse'),
			'terraced_house' => trans('web/properties.type.terraced_house'),
			'garage' => trans('web/properties.type.garage'),
			'plot' => trans('web/properties.type.plot'),
			'office' => trans('web/properties.type.office'),
            'mooring' => trans('web/properties.type.mooring'),
		];

		if ( $site_id )
		{
			$assigned = \App\Property::distinct()->select('type')->where('site_id',$site_id)->lists('type')->all();
			foreach ($options as $key => $value)
			{
				if ( !in_array($key, $assigned) )
				{
					unset($options[$key]);
				}
			}
		}

		asort($options);

		return $options;
	}

	static public function getPriceOptions($site_id = false)
	{
		$ranges = [
			'rent' => [
				'less-500' => '<500€',
				'500-750' => '501€ - 750€',
				'750-1000' => '751€ - 1.000€',
				'1000-1500' => '1.001€ - 1.500€',
				'1500-2000' => '1.501€ - 2.000€',
				'2000-more' => '> 2.000€',
			],
			'sale' => [
				'less-100000' => '< 100.000€',
				'100000-150000' => '100.001€ - 150.000€',
				'150000-200000' => '150.001€ - 200.000€',
				'200000-300000' => '200.001€ - 300.000€',
				'300000-400000' => '300.001€ - 400.000€',
				'400000-500000' => '400.001€ - 500.000€',
				'500000-750000' => '500.001€ - 750.000€',
				'750000-1000000' => '750.000€ - 1.000.000€',
				'1000000-more' => '> 1.000.000€',
			],
		];

		// Get custom ranges
		$site = \App\Site::find($site_id);

		if ($site) {
			$priceranges = $site->getGroupedPriceranges();
			foreach ($ranges as $type => $data )
			{
				if ( $priceranges->$type->count() > 0 )
				{
					$ranges[$type] = [];
					foreach ($priceranges->$type as $pricerange)
					{
						$key = implode('-', [
							$pricerange->from ? $pricerange->from : 'less',
							$pricerange->till ? $pricerange->till : 'more',
						]);
						$ranges[$type][$key] = $pricerange->title;
					}
				}
			}
		}

		// [TODO] Remove ranges without enabled properties

		return $ranges;
	}

	static public function getSizeOptions()
	{
		return [
			'40-more' => '> 40 m²',
			'60-more' => '> 60 m²',
			'80-more' => '> 80 m²',
			'100-more' => '> 100 m²',
			'120-more' => '> 120 m²',
			'140-more' => '> 140 m²',
			'160-more' => '> 160 m²',
			'180-more' => '> 180 m²',
			'200-more' => '> 200 m²',
			'400-more' => '> 400 m²',
			'600-more' => '> 600 m²',
		];
	}

	static public function getRoomOptions()
	{
		return [
			'0-more' => '> 1',
			'2-more' => '> 2',
			'3-more' => '> 3',
			'4-more' => '> 4',
			'5-more' => '> 5',
		];
	}

	static public function getBathOptions()
	{
		return [
			'0-more' => '> 1',
			'2-more' => '> 2',
			'3-more' => '> 3',
			'4-more' => '> 4',
			'5-more' => '> 5',
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
            'Exento' => 'Exento',
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


	public function getPossibleMatchesAttribute() {
		$query = $this->site->customers()->whereIn('id', function($query){
			$subquery = $query->select('customer_id')->from('customers_queries');

			if($this->mode) {
				$subquery->where(function($subquery){
					$subquery->where('mode', $this->mode);
					$subquery->orWhere('mode', '');
				});
			}

			if($this->type) {
				$subquery->where(function($subquery){
					$subquery->where('type', $this->type);
					$subquery->orWhere('type', '');
				});
			}

			if($this->price){
				$subquery->where(function($subquery){
					$subquery->where('price_min', '<=', $this->price);
					$subquery->orWhereNull('price_min');
				});

				$subquery->where(function($subquery){
					$subquery->where('price_max', '>=', $this->price);
					$subquery->orWhereNull('price_max');
				});
			}

			if($this->size) {
				$subquery->where(function($subquery){
					$subquery->where('size_min', '<=', $this->size);
					$subquery->orWhereNull('size_min');
				});

				$subquery->where(function($subquery){
					$subquery->where('size_max', '>=', $this->size);
					$subquery->orWhereNull('size_max');
				});
			}

			if($this->rooms) {
				$subquery->where(function($subquery){
					$subquery->where('rooms', '<=', $this->rooms);
					$subquery->orWhere('rooms', '');
					$subquery->orWhereNull('rooms');
				});
			}

			if($this->baths) {
				$subquery->where(function($subquery){
					$subquery->where('baths', '<=', $this->baths);
					$subquery->orWhere('baths', '');
					$subquery->orWhereNull('baths');
				});
			}

			if($this->country_id) {
				$subquery->where(function($subquery){
					$subquery->where('country_id', $this->country_id);
					$subquery->orWhereNull('country_id');
				});
			}

			if($this->territory_id ){
				$subquery->where(function($subquery){
					$subquery->where('territory_id', $this->territory);
					$subquery->orWhereNull('territory_id');
				});
			}

			if($this->state_id) {
				$subquery->where(function($subquery){
					$subquery->where('state_id', $this->state_id);
					$subquery->orWhereNull('state_id');
				});
			}

			if($this->zipcode) {
				$subquery->where(function($subquery){
					$subquery->where('zipcode', $this->zipcode);
					$subquery->orWhere('zipcode', '');
				});
			}

			return $subquery;
		})->with('queries');

		//Not current leads
		$query->whereNotIn('id', function($query){
			$query->select('customer_id')->from('properties_customers')
					->where('property_id', $this->id);
		});

		// Not discarded
		$query->whereNotIn('id', function($query){
			$query->select('customer_id')->from('properties_customers_discards')
					->where('property_id', $this->id);
		});

		$collection =  $query->get();

		$filtered = $collection->reject(function ($value, $key) {
			$query = $value->current_query;
			$attr = $query->more_attributes;

			$customerCities = $value->customer_cities()
					->pluck('city_id')->toArray();
			$customerDistricts = $value->customer_districts()
					->pluck('district_id')->toArray();

			if($this->city_id && !empty($customerCities)){
				return !in_array($this->city_id, $customerCities);
			}

			if($this->district_id && !empty($customerDistricts)){
				return !in_array($this->district_id, $customerDistricts);
			}

			if (@$attr['newly_build'] && $this->newly_build !== 1){
				return true;
			}

			if (@$attr['second_hand'] && $this->second_hand !== 1){
				return true;
			}

			if (@$attr['bank_owned'] && $this->bank_owned !== 1){
				return true;
			}

			if (@$attr['private_owned'] && $this->private_owned !== 1){
				return true;
			}

			if(@$attr['services']) {
				$serviceSlugs = !is_array($attr['services']) ?
						[$attr['services']] : $attr['services'];

				$required_service_ids = \App\Models\Property\ServiceTranslation::whereIn('slug',$serviceSlugs)->lists('service_id')->all();
				$property_service_ids = $this->services->pluck('id')->toArray();
				$intersected = array_intersect($required_service_ids, $property_service_ids);

				return count($required_service_ids) != count($intersected);
			}
		});

		return $filtered;
	}

	public function getDistrictAttribute()
	{
		if (!$this->district_id) return '';

		$district = $this->site ? $this->site->districts()->find($this->district_id) : false;
		if (!$district) return '';

		return $district->name;
	}

}
