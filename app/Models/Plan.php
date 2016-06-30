<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\ValidatorTrait;

class Plan extends Model
{
	use ValidatorTrait;

	protected $guarded = [];

	protected $casts = [
		'configuration' => 'array',
		'extras' => 'array',
	];

	protected static $create_validator_fields = [
		'code' => 'required|unique:plans,code',
		'name' => 'required',
		'is_free' => 'boolean',
		'price_year' => 'required_unless:is_free,1|numeric',
		'price_month' => 'required_unless:is_free,1|numeric',
		'max_employees' => 'integer',
		'max_properties' => 'integer',
		'max_languages' => 'integer',
		'max_space' => 'integer',
		'configuration' => 'array',
		'extras' => 'array',
		'stripe_year_id' => 'required_unless:is_free,1',
		'stripe_month_id' => 'required_unless:is_free,1',
		'level' => 'integer',
		'enabled' => 'boolean',
	];

	protected static $update_validator_fields = [
		'name' => 'required',
		'is_free' => 'boolean',
		'price_year' => 'required_unless:is_free,1|numeric',
		'price_month' => 'required_unless:is_free,1|numeric',
		'max_employees' => 'integer',
		'max_properties' => 'integer',
		'max_languages' => 'integer',
		'max_space' => 'integer',
		'configuration' => 'array',
		'extras' => 'array',
		'stripe_year_id' => 'required_unless:is_free,1',
		'stripe_month_id' => 'required_unless:is_free,1',
		'level' => 'integer',
		'enabled' => 'boolean',
	];

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
			$item = new \App\Models\Plan;
			$fields = array_keys(self::$create_validator_fields);
		}

		$data['is_free'] = empty($data['is_free']) ? 0 : 1;

		foreach ($fields as $field)
		{
			switch ($field) 
			{
				case 'price_year':
				case 'price_month':
					$item->$field = $data['is_free'] ? null : @$data[$field];
					break;
				case 'max_employees':
				case 'max_properties':
				case 'max_languages':
				case 'max_space':
					$item->$field = empty($data[$field]) ? null : $data[$field];
					break;
				case 'stripe_year_id':
				case 'stripe_month_id':
					$item->$field = $data['is_free'] ? '' : @$data[$field];
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

	public function scopeWithCode($query, $code)
	{
		return $query->where("{$this->getTable()}.code", $code);
	}

	static public function getEnabled()
	{
		return \App\Models\Plan::enabled()->orderBy('level','asc')->get()->keyBy('code');
	}

	static public function getPaymentOptions()
	{
		return [
			'stripe' => trans('account/payment.method.stripe'),
			'transfer' => trans('account/payment.method.transfer'),
		];
	}

}
