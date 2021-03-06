<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Cviebrock\EloquentSluggable\SluggableInterface;

use App\Traits\SluggableTrait;
use OwenIt\Auditing\AuditingTrait;

class PropertyTranslation extends Model implements SluggableInterface
{
	protected $table = 'properties_translations';

	use SluggableTrait;
	protected $sluggable = [
		'build_from' => 'title',
		'save_to'    => 'slug',
		'unique' => false,
	];

	use AuditingTrait;
	protected $dontKeepLogOf = [ 'slug' ];
	protected $auditableTypes = [ 'created', 'saved' ];
	public static $logCustomMessage = '{user.name|Anonymous} {type} this property {elapsed_time}';
	public static $logCustomFields = [];

	public static function boot()
	{
		parent::boot();

		static::$logCustomFields = [
			'title'  => trans('account/properties.ref'),
			'description'  => trans('account/properties.type'),
			'label'  => trans('account/properties.mode'),
		];
	}

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
			$this->whereIn('properties_translations.property_id', function($query) use($item) {
				$query->select('id')
					->from('properties')
					->where('site_id', function($query) use ($item) {
						$query->select('site_id')
							->from('properties')
							->where('id', $item->property_id);
					});

			})->where('properties_translations.slug',$item->slug)->where('properties_translations.property_id','!=',$item->property_id)->count() > 0 
		)
		{
			$i++;
			$item->slug = "{$slug}-{$i}";
		}

		return $item;
	}

}
