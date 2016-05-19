<?php namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class Stats extends Model
{
    protected $table = 'users_stats';

	protected $guarded = [];

	public $timestamps = false;

	public function user()
	{
		return $this->belongsTo('App\User');
	}

}
