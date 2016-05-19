<?php namespace App\Models\Site;

use Illuminate\Database\Eloquent\Model;

class Stats extends Model
{
    protected $table = 'sites_stats';

	protected $guarded = [];

	public $timestamps = false;

	public function site()
	{
		return $this->belongsTo('App\Site');
	}

}
