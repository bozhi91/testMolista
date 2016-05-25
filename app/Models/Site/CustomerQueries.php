<?php

namespace App\Models\Site;

use Illuminate\Database\Eloquent\Model;

class CustomerQueries extends Model
{
	protected $guarded = [];

	protected $table = 'customers_queries';

	public function customer()
	{
		return $this->belongsTo('App\Models\Site\Customer');
	}

	public function getPriceRangeAttribute()
	{
		 return "{$this->price_min}-{$this->price_max}";
	}

	public function getSizeRangeAttribute()
	{
		 return "{$this->size_min}-{$this->size_max}";
	}

	public function getMoreAttributesAttribute($value)
	{
		$value = @unserialize($value);
		 return is_array($value) ? $value : [];
	}
    public function setMoreAttributesAttribute($value)
    {
        $this->attributes['more_attributes'] = serialize( is_array($value) ? $value : [] );
    }

	public function scopeEnabled($query)
	{
		$query->where('enabled', 1);
	}

}
