<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurrencyTranslation extends Model
{
	protected $table = 'currencies_translations';

	protected $guarded = [];

	public $timestamps = false;
}
