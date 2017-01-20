<?php

namespace App\Models\Site;

use Illuminate\Database\Eloquent\Model;

class SliderGroupLocale extends Model {

	protected $table = 'slider_group_locale';
	protected $guarded = [];

	public function group() {
		return $this->belongsTo('App\Models\Site\SliderGroup');
	}

	public function locale() {
		return $this->belongsTo('App\Models\Locale');		
	}

}
