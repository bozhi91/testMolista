<?php

namespace App\Traits;

use Cviebrock\EloquentSluggable\SluggableTrait as EloquentSluggableTrait;

trait SluggableTrait
{
	use EloquentSluggableTrait {
		EloquentSluggableTrait::sluggify as sluggableSluggify;
    }

	public function sluggify($force = false)
	{
		$item = $this->sluggableSluggify($force);

		$instance = new static;
		if ( method_exists($instance, 'checkSlugUniqueness') )
		{
			$item = $instance->checkSlugUniqueness($item);
		}

		return $item;
	}
}
