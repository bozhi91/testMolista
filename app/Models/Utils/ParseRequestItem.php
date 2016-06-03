<?php namespace App\Models\Utils;

use Illuminate\Database\Eloquent\Model;

class ParseRequestItem extends Model
{
	protected $table = 'parse_requests_items';
	protected $guarded = [];

	protected $casts = [
		'columns' => 'array',
	];

	public function request()
	{
		return $this->belongsTo('App\Models\Utils\ParseRequest');
	}

}
