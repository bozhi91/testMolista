<?php

namespace App\Models\Site;

use \App\TranslatableModel;

class MenuItem extends TranslatableModel
{
	public $translatedAttributes = ['title', 'url'];

    protected $table = 'menus_items';

	protected $guarded = [];

    public $timestamps = false;

	public function menu()
	{
		return $this->belongsTo('App\Models\Site\Menu');
	}

	public function property()
	{
		return $this->belongsTo('App\Property');
	}

	public function page()
	{
		return $this->belongsTo('App\Models\Site\Page');
	}

	public function getItemTitleAttribute()
	{
		if ( $this->title )
		{
			return $this->title;
		}

		switch ( $this->type )
		{
			case 'property':
				return $this->property->title;
			case 'page':
				return $this->page->title;
		}

		return "Item {$this->id}";
	}

	public function getItemUrlAttribute()
	{
		switch ( $this->type )
		{
			case 'custom':
				return $this->url;
			case 'property':
				return action('Web\PropertiesController@details', $this->property->slug);
			case 'page':
				return action('Web\PagesController@show', $this->page->slug);
		}

		return false;
	}

}
