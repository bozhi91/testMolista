<?php namespace App\Models\Property;

use Illuminate\Database\Eloquent\SoftDeletes;

class Documents extends \Illuminate\Database\Eloquent\Model
{
	use SoftDeletes;
	protected $dates = ['date','deleted_at'];

	protected $table = 'properties_documents';

	protected $guarded = [];

	public function property()
	{
		return $this->belongsTo('App\Property')->with('site')->withTranslations();
	}

	public function user()
	{
		return $this->belongsTo('App\User');
	}

	public function getFileDirectoryAttribute()
	{
		return storage_path("sites/{$this->property->site->id}/property/{$this->property_id}/documents");
	}

	public function scopeOfType($query, $type)
	{
		return $query->where("{$this->getTable()}.type", $type);
	}

}
