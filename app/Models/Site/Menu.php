<?php

namespace App\Models\Site;

use Illuminate\Database\Eloquent\Model;

use Cviebrock\EloquentSluggable\SluggableInterface;

use App\Traits\SluggableTrait;

class Menu extends Model implements SluggableInterface
{
	use SluggableTrait;

	protected $sluggable = [
		'build_from' => 'title',
		'save_to'    => 'slug',
	];

	protected $guarded = [];

	public function items() {
		return $this->hasMany('App\Models\Site\MenuItem')->withTranslations()->with('property')->with('page')->orderBy('position');
	}

	public function scopeWithItems($query)
	{
		return $query;
	}

}
