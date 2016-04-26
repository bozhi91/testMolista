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

	public function getImageFolderAttribute()
	{
		return "sites/{$this->site_id}/pages/{$this->id}";
	}
	public function getImageDirAttribute()
	{
		return asset( $this->image_folder );
	}

}
