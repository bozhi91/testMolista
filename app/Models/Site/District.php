<?php

namespace App\Models\Site;

use Illuminate\Database\Eloquent\Model;

class District extends Model {

	protected $table = 'districts';
	protected $guarded = [];

	public function site() {
		return $this->belongsTo('App\Site')->withTranslations();
	}

}
