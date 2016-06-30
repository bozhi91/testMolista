<?php namespace App\Models\Site;

use Illuminate\Database\Eloquent\Model;

class Webhook extends Model
{
    protected $table = 'sites_webhooks';

	protected $guarded = [];

	protected $casts = [
		'data' => 'array',
	];

	public function site()
	{
		return $this->belongsTo('App\Site');
	}

}
