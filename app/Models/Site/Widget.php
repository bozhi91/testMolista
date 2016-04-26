<?php

namespace App\Models\Site;

use \App\TranslatableModel;

class Widget extends TranslatableModel
{
	public $translatedAttributes = [ 'title', 'content' ];

	protected $guarded = [];

	public function menu()
	{
		return $this->belongsTo('App\Models\Site\Menu');
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
		];
	}

	static public function getGroupOptions() 
	{
		return [
			'header' => [
				'accept' => 'menu',
				'max' => 1,
			],
			'footer' => [
			],
		];
	}

}
