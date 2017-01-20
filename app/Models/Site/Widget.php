<?php

namespace App\Models\Site;

use \App\TranslatableModel;

class Widget extends TranslatableModel
{
	public $translatedAttributes = [ 'title', 'content' ];

	protected $guarded = [];

    protected $casts = [
        'data' => 'array',
    ];
	
	public function menu()
	{
		return $this->belongsTo('App\Models\Site\Menu');
	}

	public function slider()
	{
		return $this->belongsTo('App\Models\Site\SliderGroup');
	}
	
	public function scopeWithMenu($query)
	{
		return $query->with(['menu' => function($query){
			$query->WithItems();
		}]);
	}
	public function scopeOfGroup($query, $group)
	{
		return $query->where('widget.group', $group);
	}

	public function scopeOfType($query, $type)
	{
		return $query->where('widget.type', $type);
	}

	static public function getTypeOptions() 
	{
		return [
			'menu',
			'text',
			'slider',
			'awesome-link',
		];
	}

	static public function getGroupOptions() 
	{
		$widget_groups = \Theme::config('widget-groups');

		return is_array($widget_groups) ? $widget_groups : [];
	}

}
