<?php

namespace App\Models\Site;

use Illuminate\Database\Eloquent\Model;

use Cviebrock\EloquentSluggable\SluggableInterface;

use App\Traits\SluggableTrait;

class PageTranslation extends Model implements SluggableInterface
{
	protected $table = 'pages_translations';

	use SluggableTrait;

	protected $sluggable = [
		'build_from' => 'title',
		'save_to' => 'slug',
		'unique' => false,
	];

	protected $guarded = [];

	public $timestamps = false;

	public function checkSlugUniqueness($item)
	{
		if ( !$item->slug )
		{
			return $item;
		}

		$i = 0;
		$slug = $item->slug;
		while ( 
			$this->whereIn('pages_translations.page_id', function($query) use($item) {
				$query->select('id')
					->from('pages')
					->where('site_id', function($query) use ($item) {
						$query->select('site_id')
							->from('pages')
							->where('id', $item->page_id);
					});

			})->where('pages_translations.slug',$item->slug)->where('pages_translations.page_id','!=',$item->page_id)->count() > 0 
		)
		{
			$i++;
			$item->slug = "{$slug}-{$i}";
		}

		return $item;
	}

}
