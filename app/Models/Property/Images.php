<?php

namespace App\Models\Property;

use Illuminate\Database\Eloquent\Model;

class Images extends Model
{
	protected $table = 'properties_images';

	protected $guarded = [];

	public $timestamps = false;

	public function property()
	{
		return $this->belongsTo('App\Property');
	}

}