<?php namespace App\Models\Site;

use Illuminate\Database\Eloquent\SoftDeletes;

class Planchange extends \Illuminate\Database\Eloquent\Model
{
	use SoftDeletes;

    protected $table = 'sites_planchanges';

	protected $guarded = [];

	protected $dates = ['deleted_at'];

	protected $casts = [
		'old_data' => 'array',
		'new_data' => 'array',
		'invoicing' => 'array',
	];

	public function site()
	{
		return $this->belongsTo('App\Site')->withTranslations();
	}

	public function scopePending($query)
	{
		return $query->where("{$this->getTable()}.status", 'pending');
	}

	public function scopeActive($query)
	{
		return $query->where("{$this->getTable()}.status", 'active');
	}

}
