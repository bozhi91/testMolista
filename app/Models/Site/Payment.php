<?php namespace App\Models\Site;

use Illuminate\Database\Eloquent\Model;

use App\Traits\ValidatorTrait;

class Payment extends Model
{
	use ValidatorTrait;

    protected $table = 'sites_payments';

	protected $guarded = [];

	protected $dates = [ 'paid_from','paid_until','reseller_date' ];

	protected $casts = [
		'data' => 'array',
	];

	protected static $create_validator_fields = [
		'site_id' => 'required|exists:sites,id',
		'plan_id' => 'required|exists:plans,id',
		'trigger' => 'required',
		'paid_from' => 'required|date_format:"Y-m-d"',
		'paid_until' => 'required|date_format:"Y-m-d"',
		'payment_method' => 'required',
		'payment_amount' => 'required|numeric',
		'payment_currency' => 'required|exists:currencies,code',
		'payment_rate' => 'required|numeric',
		'payment_vat' => 'numeric',
		'reseller_id' => 'exists:resellers,id',
		'reseller_variable' => 'numeric',
		'reseller_fixed' => 'numeric',
		'reseller_amount' => 'numeric',
		'reseller_paid' => 'boolean',
		'reseller_date' => 'date_format:"Y-m-d"',
		'reseller_rate' => 'numeric',
		'data' => 'array',
		'created_by' => 'exists:users,id',
	];

	protected static $update_validator_fields = [
		'paid_from' => 'required|date_format:"Y-m-d"',
		'paid_until' => 'required|date_format:"Y-m-d"',
		'payment_method' => 'required',
		'payment_amount' => 'required|numeric',
		'payment_currency' => 'required|exists:currencies,code',
		'payment_rate' => 'required|numeric',
		'payment_vat' => 'numeric',
		'reseller_id' => 'exists:resellers,id',
		'reseller_variable' => 'numeric',
		'reseller_fixed' => 'numeric',
		'reseller_amount' => 'numeric',
		'reseller_paid' => 'boolean',
		'reseller_date' => 'date_format:"Y-m-d"',
		'reseller_rate' => 'numeric',
	];

	public function site()
	{
		return $this->belongsTo('App\Site');
	}

	public function plan()
	{
		return $this->belongsTo('App\Models\Plan');
	}

	public function reseller()
	{
		return $this->belongsTo('App\Models\Reseller');
	}

	public function infocurrency()
	{
		return $this->hasOne('App\Models\Currency', 'code', 'payment_currency')->withTranslations();
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
			$item = new self();
			$fields = array_keys(self::$create_validator_fields);
		}

		foreach ($fields as $field)
		{
			if ( !isset($data[$field]) )
			{
				continue;
			}

			switch ($field) 
			{
				case 'reseller_id':
				case 'reseller_date':
				case 'created_by':
					$item->$field = @$data[$field] ? $data[$field] : null;
					break;
				default:
					$item->$field = @$data[$field];
			}
		}

		$item->save();

		return $item;
	}

}
