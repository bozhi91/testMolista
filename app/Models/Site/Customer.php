<?php

namespace App\Models\Site;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
	protected $guarded = [];


	public function getFullNameAttribute()
	{
		return implode(' ', [
			@$this->attributes['first_name'],
			@$this->attributes['last_name']
		]);
	}

	public function scopeWithFullName($query, $full_name)
	{
		$query->whereRaw("CONCAT(customers.`first_name`,' ',customers.`last_name`) LIKE '%" . \DB::connection()->getPdo()->quote($full_name) . "%'");
	}

}
