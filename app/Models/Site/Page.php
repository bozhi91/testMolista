<?php

namespace App\Models\Site;

use \App\TranslatableModel;

class Page extends TranslatableModel
{
	public $translatedAttributes = ['title', 'slug', 'body', 'seo_title', 'seo_description', 'seo_keywords'];

	protected $guarded = [];

	public function scopeEnabled($query)
	{
		return $query->where('enabled', 1);
	}

	public function setConfigurationAttribute($value)
	{
		$this->attributes['configuration'] = serialize($value);
	}
	public function getConfigurationAttribute($value)
	{
		$configuration = @unserialize($value);

		return empty($configuration) ? [] : $configuration;
	}

	public function getImageFolderAttribute()
	{
		return "sites/{$this->site_id}/pages/{$this->id}";
	}
	public function getImageDirAttribute()
	{
		return asset( $this->image_folder );
	}

	static public function getTypeOptions() 
	{
		return [
			'default' => trans('account/site.pages.type.default'),
			'contact' => trans('account/site.pages.type.contact'),
			'map' => trans('account/site.pages.type.map'),
		];
	}

}
