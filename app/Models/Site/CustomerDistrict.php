<?php

namespace App\Models\Site;

use Illuminate\Database\Eloquent\Model;

class CustomerDistrict extends Model {

	protected $table = 'customers_districts';
	protected $guarded = [];

	public function customer() {
		return $this->belongsTo('App\Models\Site\Customer');
	}

	public function district() {
		return $this->belongsTo('App\Models\Site\District');
	}

}
