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

	public function getFullNameAttribute()
	{
		return implode(' ', [
			@$this->attributes['first_name'],
			@$this->attributes['last_name']
		]);
	}

	public function getCurrentQueryAttribute()
	{
		return $this->queries->where('enabled',1)->first();
	}

	public function scopeWithFullName($query, $full_name)
	{
		$query->where(\DB::raw("CONCAT(customers.`first_name`,' ',customers.`last_name`)"), 'like', "%$full_name%");
	}

	public function scopeOfUser($query, $user_id)
	{
		$query->where(function($query) use ($user_id) {
			$query->whereIn('customers.id', function($query) use ($user_id) {
				$query->distinct()->select('customer_id')
					->from('properties_customers')
					->whereIn('property_id', function($query) use ($user_id) {
						$query->distinct()->select('property_id')
							->from('properties_users')
							->where('user_id', $user_id);
					});
			})->orWhere('customers.created_by', $user_id);
		});
	}

	public function getPossibleMatchesAttribute() {
		$query = $this->site->properties();

		$params = $this->current_query;

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

		// City
		if ( $params->city_id )
		{
			$query->where('city_id', $params->city_id);
		}

		// District
		if ( $params->district )
		{
			$query->where('district', 'LIKE', "%{$params->district}%");
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
