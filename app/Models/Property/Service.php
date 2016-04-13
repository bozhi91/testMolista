<?php

namespace App\Models\Property;

use \App\TranslatableModel;

class Service extends TranslatableModel
{
	public $translatedAttributes = [ 'title', 'description', 'slug' ];

	protected $guarded = [];

	public function scopeEnabled($query)
	{
		return $query->where('enabled', 1);
	}

}
