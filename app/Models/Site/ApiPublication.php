<?php

namespace App\Models\Site;

use Illuminate\Database\Eloquent\Model;

class ApiPublication extends Model {

	protected $table = 'api_publications';
	protected $guarded = [];

	public function site() {
		return $this->belongsTo('App\Site');
	}

	public function marketplace() {
		return $this->belongsTo('App\Models\Marketplace');
	}

	protected $casts = [
		'property' => 'array',
		'result' => 'array',
	];

}
