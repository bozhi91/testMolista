<?php

namespace App\Models\Site;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
	protected $guarded = [];

	public function site()
	{
		return $this->belongsTo('App\Site');
	}

	public function properties() {
		return $this->belongsToMany('App\Property', 'properties_customers', 'customer_id', 'property_id');
	}

	public function queries() {
		return $this->hasMany('App\Models\Site\CustomerQueries');
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
		$query->whereRaw("CONCAT(customers.`first_name`,' ',customers.`last_name`) LIKE '%" . \DB::connection()->getPdo()->quote($full_name) . "%'");
	}

	public function getPossibleMatchesAttribute() {
		$query = \App\Site::findOrFail($this->site_id)->properties();

		$params = $this->current_query;

		if ( !$params )
		{
			return $query->where('properties.id',0)->get();
		}

		// Not current properties
		$query->whereNotIn('id', function($query){
			$query->select('property_id')->from('properties_customers')->where('customer_id', $this->id);
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

		// [TODO] Tags
		// [TODO] Services

		return $query->get();

	}

}
