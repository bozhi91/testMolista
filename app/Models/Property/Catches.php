<?php

namespace App\Models\Property;

use Illuminate\Database\Eloquent\Model;

class Catches extends Model
{
	protected $table = 'properties_catches';

	protected $guarded = [];

	public $timestamps = false;

	public function getDates()
	{
		return array('catch_date','transaction_date');
	}

	public function property()
	{
		return $this->belongsTo('App\Property');
	}

	public function employee()
	{
		return $this->belongsTo('App\User', 'employee_id');
	}

	public function seller()
	{
		return $this->belongsTo('App\User', 'closer_id');
	}

	public function buyer()
	{
		return $this->belongsTo('App\Models\Site\Customer', 'buyer_id');
	}

	public function getSellerFullNameAttribute()
	{
		$fullname = array_filter([
			$this->seller_first_name,
			$this->seller_last_name,
		]);

		return empty($fullname) ? false : implode(' ', $fullname);
	}

	public function getCommissionEarnedAttribute()
	{
		return @floatval( $this->price_sold * $this->commission / 100 );
	}

	// [TODO]
	public function getLeadsTotalAttribute()
	{
		return 0;
	}

	public function scopeOfSite($query, $site_id)
	{
		return $query->whereIn('properties_catches.property_id', function($query) use($site_id) {
			$query->select('id')->from('properties')->where('site_id', $site_id);
		});
	}


}