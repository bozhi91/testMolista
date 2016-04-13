<?php

namespace App\Models\Geography;

use Illuminate\Database\Eloquent\Model;

use Cviebrock\EloquentSluggable\SluggableInterface;
use Cviebrock\EloquentSluggable\SluggableTrait;

class City extends Model implements SluggableInterface
{
	use SluggableTrait;

	protected $sluggable = [
		'build_from' => 'name',
		'save_to'    => 'slug',
	];

	protected $guarded = [];

	public function properties() {
		return $this->hasMany('App\Property');
	}

	public function scopeEnabled($query)
	{
		return $query->where('cities.enabled', 1);
	}

	public function scopeWithStateSlug($query, $slug)
	{
		return $query->join('states', function($join) use ($slug) {
			$join->on('cities.state_id', '=', 'states.id');
			$join->on('states.slug', '=', \DB::raw( \DB::connection()->getPdo()->quote($slug) ) );
		});
	}

}
