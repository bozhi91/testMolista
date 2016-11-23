<?php

namespace App\Models\Site;

use Illuminate\Database\Eloquent\Model;

class CustomerCity extends Model {

	protected $table = 'customers_cities';
	protected $guarded = [];

	public function customer() {
		return $this->belongsTo('App\Models\Site\Customer');
	}

	public function city() {
		return $this->belongsTo('App\Models\Geography\City');
	}

}
