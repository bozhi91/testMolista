<?php

namespace App\Models\Geography;

use \App\TranslatableModel;

class Country extends TranslatableModel
{
	public $translatedAttributes = ['name'];

	protected $guarded = [];

	public function properties() {
		return $this->hasMany('App\Property');
	}

	public function scopeEnabled($query)
	{
		return $query->where('enabled', 1);
	}

}
