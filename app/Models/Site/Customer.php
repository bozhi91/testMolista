<?php

namespace App\Models\Site;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
	protected $guarded = [];

	protected $casts = [
		'alert_config' => 'array',
	];

	public static function boot()
	{
		parent::boot();

		// Whenever a customer is created
		static::created(function($item){
			$customer = self::find($item->id);
			$customer->site->ticket_adm->associateContact($customer);
			// Si tenemos creador, lo vinculamos al cliente
			if ($customer->created_by) {
				$customer->users()->attach($customer->created_by);
			}
		});
		// Whenever a customer is updated
		static::updated(function($item){
			$customer = self::find($item->id);
			$customer->site->ticket_adm->associateContact($customer);
		});
	}


	public function site()
	{
		return $this->belongsTo('App\Site')->withTranslations();
	}

	public function calendars() {
		return $this->hasMany('App\Models\Calendar');
	}

	public function properties() {
		return $this->belongsToMany('App\Property', 'properties_customers', 'customer_id', 'property_id')->withTranslations();
	}

	public function properties_discards() {
		return $this->belongsToMany('App\Property', 'properties_customers_discards', 'customer_id', 'property_id')->withTranslations();
	}

	public function queries() {
		return $this->hasMany('App\Models\Site\CustomerQueries')->with('infocurrency');
	}

	public function customer_districts(){
		return $this->hasMany('App\Models\Site\CustomerDistrict', 'customer_id');
	}

	public function customer_cities(){
		return $this->hasMany('App\Models\Site\CustomerCity', 'customer_id');
	}

	public function users() {
		return $this->belongsToMany('\App\User')->withTimestamps();
	}

	public function getFullNameAttribute()
	{
		return implode(' ', [
			@$this->attributes['first_name'],
			@$this->attributes['last_name']
		]);
	}

	public function getCurrentQueryAttribute()
	{
		return $this->queries()->where('enabled',1)->first();
	}

	public function scopeWithFullName($query, $full_name)
	{
		$query->where(\DB::raw("CONCAT(customers.`first_name`,' ',customers.`last_name`)"), 'like', "%$full_name%");
	}

	public function scopeOfUser($query, $user_id)
	{
		$query->whereIn('customers.id', \DB::table('customer_user')->select('customer_id')->where('user_id', $user_id)->pluck('customer_id'));
	}

	public function getPossibleMatchesAttribute() {
		$query = $this->site->properties()->enabled();

		$params = $this->current_query;

		$district_ids = $this->customer_districts()->pluck('district_id')->toArray();
		$city_ids = $this->customer_cities()->pluck('city_id')->toArray();

		if ( !$params )
		{
			return $query->where('properties.id',0)->get();
		}

		// Not current properties
		$query->whereNotIn('properties.id', function($query){
			$query->select('property_id')->from('properties_customers')->where('customer_id', $this->id);
		});

		// Not discarded
		$query->whereNotIn('properties.id', function($query){
			$query->select('property_id')->from('properties_customers_discards')->where('customer_id', $this->id);
		});

		// Mode
		if ( $params->mode )
		{
			$query->where('mode', $params->mode);
		}

		// Type
		if ( $params->type )
		{
			$query->where('type', $params->type);
		}

		// Price
		$query->withPriceBetween($params->price_range, $params->currency);

		// Size
		$query->withSizeBetween($params->size_range, $params->size_unit);

		// Rooms
		$query->withRange('rooms', $params->rooms);

		// Bathrooms
		$query->withRange('baths', $params->baths);

		// Country
		if ( $params->country_id )
		{
			$query->where('country_id', $params->country_id);
		}

		// Territory
		if ( $params->territory_id )
		{
			$query->where('territory_id', $params->territory_id);
		}

		// State
		if ( $params->state_id )
		{
			$query->where('state_id', $params->state_id);
		}

		if(!empty($city_ids)) {
			$query->whereIn('city_id', $city_ids);
		}

		if(!empty($district_ids)) {
			$query->whereIn('district_id', $district_ids);
		}

		// Zipcode
		if ( $params->zipcode )
		{
			$query->where('zipcode', $params->zipcode);
		}

		// More attributes
		$attr = $params->more_attributes;

		// Newly build
		if ( @$attr['newly_build'] )
		{
			$query->where('newly_build', 1);
		}

		// Second hand
		if ( @$attr['second_hand'] )
		{
			$query->where('second_hand', 1);
		}

		// Bank owned
		if ( @$attr['bank_owned'] )
		{
			$query->where('bank_owned', 1);
		}

		// Private owned
		if ( @$attr['private_owned'] )
		{
			$query->where('private_owned', 1);
		}

		// Services
		if ( @$attr['services'] )
		{
			$query->withServices($attr['services']);
		}

		return $query->get();

	}

}
