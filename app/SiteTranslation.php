<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SiteTranslation extends Model
{
	protected $table = 'sites_translations';

	protected $guarded = [];

	public $timestamps = false;
}
