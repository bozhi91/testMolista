<?php

namespace App\Models\Site;

use Illuminate\Database\Eloquent\Model;

class SliderImage extends Model {

	protected $table = 'slider_image';
	protected $guarded = [];
	
	public function group() {
		return $this->belongsTo('App\SliderGroup');
	}
}
