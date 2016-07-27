<?php

namespace App\Models\Geography;

use \App\TranslatableModel;

use App\Traits\ValidatorTrait;

class Country extends TranslatableModel
{
	public $translatedAttributes = ['name'];

	protected $guarded = [];

	protected $casts = [
		'pay_methods' => 'array',
		'marketplaces_images' => 'array',
	];

	use ValidatorTrait;
	protected static $create_validator_fields = [
		'code' => 'required|unique:countries,code',
		'currency' => 'required|exists:currencies,code',
		'locale' => 'required|exists:locales,locale',
		'pay_methods' => 'array',
		'feature_image' => 'image',
		'marketplaces_image' => 'image',
		'enabled' => 'boolean',
		'i18n.name' => 'required|array',
	];
	protected static $update_validator_fields = [
		'currency' => 'required|exists:currencies,code',
		'locale' => 'required|exists:locales,locale',
		'pay_methods' => 'array',
		'feature_image' => 'image',
		'marketplaces_image' => 'image',
		'enabled' => 'boolean',
		'i18n.name' => 'required|array',
	];

	public function properties() {
		return $this->hasMany('App\Property');
	}

	public function states() {
		return $this->hasMany('App\Models\Geography\State');
	}

	public function cities() {
		return $this->hasManyThrough('App\Models\Geography\City','App\Models\Geography\State');
	}

	public function marketplaces() {
		return $this->belongsToMany('App\Models\Marketplace', 'marketplaces_countries', 'country_id', 'marketplace_id');
	}

	public function getItemsFolderAttribute()
	{
		return "configured/countries/{$this->id}";
	}

	public static function saveModel($data, $id = null)
	{
		if ($id)
		{
			$item = self::find($id);
			if (!$item)
			{
				return false;
			}
			$fields = array_keys(self::$update_validator_fields);
		}
		else
		{
			$item = new \App\Models\Geography\Country;
			$fields = array_keys(self::$create_validator_fields);
		}

		foreach ($fields as $field)
		{
			switch ($field)
			{
				case 'feature_image':
				case 'marketplaces_image':
					break;
				case 'i18n.name':
					$field_parts = explode('.', $field);
					foreach ($data[$field_parts[0]][$field_parts[1]] as $locale => $value)
					{
						$item->translateOrNew($locale)->$field_parts[1] = $value;
					}
					break;
				case 'enabled':
					$item->$field = empty($data[$field]) ? 0 : 1;
					break;
				default:
					$item->$field = @$data[$field];
			}
		}

		$item->save();

		return $item;
	}

	public static function saveImages($item,$request)
	{
		// Check new feature_image
		if ( $request->file('feature_image') )
		{
			// Delete old feature_image
			if ( $item->feature_image )
			{
				@unlink( public_path("{$item->items_folder}/{$item->feature_image}") );
			}

			// Move new feature_image
			$item->feature_image = $request->file('feature_image')->getClientOriginalName();
			while ( file_exists( public_path("{$item->items_folder}/{$item->feature_image}") ) )
			{
				$item->feature_image = uniqid() . '_' . $request->file('feature_image')->getClientOriginalName();
			}
			$request->file('feature_image')->move( public_path($item->items_folder), $item->feature_image );
		}
		// Check delete old feature_image
		elseif ( $request->input('feature_image_remove') )
		{
			if ( $item->feature_image )
			{
				@unlink( public_path("{$item->items_folder}/{$item->feature_image}") );
			}
			$item->feature_image = '';
		}


		// Delete non confirmed logos
		$current_marketplaces_images = $item->marketplaces_images;
		if ( !$current_marketplaces_images )
		{
			$current_marketplaces_images = [];
		}

		$new_marketplaces_images = $request->input('marketplaces_images');
		if ( !$new_marketplaces_images )
		{
			$new_marketplaces_images = [];
		}

		// Delete missing marketplaces_images
		foreach ($current_marketplaces_images as $image)
		{
			if ( !in_array($image, $new_marketplaces_images) )
			{
				@unlink( public_path("configured/countries/{$item->id}/{$image}") );
			}
		}

		// Check new marketplaces_image
		if ( $request->file('marketplaces_image') )
		{
			// Move new feature_image
			$marketplaces_image = $request->file('marketplaces_image')->getClientOriginalName();
			while ( file_exists( public_path("{$item->items_folder}/{$marketplaces_image}") ) )
			{
				$marketplaces_image = uniqid() . '_' . $request->file('marketplaces_image')->getClientOriginalName();
			}
			$request->file('marketplaces_image')->move( public_path($item->items_folder), $marketplaces_image );
			// Add to marketplaces_images
			$new_marketplaces_images[] = $marketplaces_image;
		}

		$item->marketplaces_images = $new_marketplaces_images;

		return $item->save();
	}

	public function scopeEnabled($query)
	{
		return $query->where('countries.enabled', 1);
	}

	public function scopeWithMarketplaces($query)
	{
		return $query->whereIn('countries.id', function($query){
			$query
				->distinct()
				->select('country_id')
				->from('marketplaces_countries')
				;
		});
	}

}
