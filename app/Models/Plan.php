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
		'currency' => 'required|exists:currencies,code',
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
		'level' => 'required|integer',
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
		'level' => 'required|integer',
		'enabled' => 'boolean',
	];

	public function infocurrency()
	{
		return $this->hasOne('App\Models\Currency', 'code', 'currency')->withTranslations();
	}

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

	static public function getEnabled($currency=false)
	{
		// 
		$authuserid = false;
		if ( $authuserid && \Auth::check() && \Auth::user()->id == $authuserid )
		{
			$ids = \App\Models\Plan::whereIn('code', ['free','pro','pro_usd','enterprise','enterprise_usd'])->lists('id');
			$query = \App\Models\Plan::whereIn('id', $ids);
		}
		else
		{
			$query = \App\Models\Plan::enabled();
		}

		if ( $currency )
		{
			$query->where(function($query) use ($currency) {
				$query
					->whereNull('plans.currency')
					->orWhere('plans.currency', '=', $currency);
			});
		}

		return $query->orderBy('plans.level','asc')->get()->keyBy('code');
	}

	static public function getPaymentOptions($valid_options=false)
	{
		$options = [
			'stripe' => trans('account/payment.method.stripe'),
		];

		if ( env('TRANSFER_PAYMENTS_ENABLED', false) )
		{
			$options['transfer'] = trans('account/payment.method.transfer');
		}

		if ( is_array($valid_options) )
		{
			foreach ($options as $key => $value)
			{
				if ( in_array($key, $valid_options) )
				{
					continue;
				}

				unset($options[$key]);
			}
		}

		return $options;
	}

}
