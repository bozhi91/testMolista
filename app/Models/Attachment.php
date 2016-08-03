<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
	protected $guarded = [];

	public function site()
	{
		return $this->belongsTo('App\Site');
	}

	public function user()
	{
		return $this->belongsTo('App\User');
	}

}
