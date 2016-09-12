<?php namespace App\Models;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\ValidatorTrait;

class Currency extends \App\TranslatableModel
{
	use SoftDeletes;
	protected $dates = ['deleted_at'];

	use Translatable;
	public $translatedAttributes = ['title'];

	use ValidatorTrait;
	protected static $create_validator_fields = [
		'code' => 'required|unique:currencies,code',
		'symbol' => 'required|string',
		'decimals' => 'required|integer',
		'position' => 'required|in:before,after',
		'enabled' => 'boolean',
		'i18n.title' => 'required|array',
	];
	protected static $update_validator_fields = [
		'symbol' => 'required|string',
		'decimals' => 'required|integer',
		'position' => 'required|in:before,after',
		'enabled' => 'boolean',
		'i18n.title' => 'required|array',
	];

	protected $guarded = [];

	public static function saveModel($data, $id = null)
	{
		if ($id)
		{
			$item = self::find($id);
			if (!$item)
			{
				return false;
			}
			$fields = array_keys(self::$update_validator_fields);
		}
		else
		{
			$item = new \App\Models\Currency;
			$fields = array_keys(self::$create_validator_fields);
		}

		foreach ($fields as $field)
		{
			switch ($field)
			{
				case 'i18n.title':
					$field_parts = explode('.', $field);
					$field_name = $field_parts[1];
					foreach ($data[$field_parts[0]][$field_name] as $locale => $value)
					{
						$item->translateOrNew($locale)->$field_name = $value;
					}
					break;
				case 'enabled':
					$item->$field = empty($data[$field]) ? 0 : 1;
					break;
				default:
					$item->$field = @$data[$field];
			}
		}

		$item->save();

		return $item;
	}

	public function scopeEnabled($query)
	{
		return $query->where("{$this->getTable()}.enabled", 1);
	}

}
