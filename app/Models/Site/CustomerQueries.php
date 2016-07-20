<?php

namespace App\Models\Site;

use Illuminate\Database\Eloquent\Model;

class CustomerQueries extends Model
{
	protected $guarded = [];

	protected $table = 'customers_queries';

	protected $casts = [
		'more_attributes' => 'array',
	];

	public function customer()
	{
		return $this->belongsTo('App\Models\Site\Customer');
	}

	public function infocurrency()
	{
		return $this->hasOne('App\Models\Currency', 'code', 'currency')->withTranslations();
	}

	public function getPriceRangeAttribute()
	{
		 return "{$this->price_min}-{$this->price_max}";
	}

	public function getSizeRangeAttribute()
	{
		 return "{$this->size_min}-{$this->size_max}";
	}

	public function scopeEnabled($query)
	{
		$query->where('enabled', 1);
	}

}
