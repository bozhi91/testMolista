<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\ValidatorTrait;

class Reseller extends Model
{
	use SoftDeletes;
	use ValidatorTrait;

	protected $guarded = [];

	protected $casts = [
		'details' => 'array',
	];

	protected $dates = ['deleted_at'];

	protected static $create_validator_fields = [
		'ref' => 'required|min:6,|max:20|alpha_num|unique:resellers,ref',
		'type' => 'required|in:individual,company',
		'name' => 'required',
		'email' => 'required|email|unique:resellers,email',
		'password' => 'required|min:6,|max:20',
		'locale' => 'required:exists:locales,locale',
		'details' => 'array',
		'details' => 'array',
		'enabled' => 'boolean',
		'plans_commissions' => 'array',
		'plans_commissions.*' => 'array',
	];

	protected static $update_validator_fields = [
		'ref' => 'required|min:6,|max:20|alpha_num|unique:resellers,ref',
		'type' => 'required|in:individual,company',
		'name' => 'required',
		'email' => 'required|email|unique:resellers,email',
		'password' => 'min:6,|max:20',
		'locale' => 'required:exists:locales,locale',
		'details' => 'array',
		'enabled' => 'boolean',
		'plans_commissions' => 'array',
		'plans_commissions.*' => 'array',
	];

	public function plans()
	{
		return $this->belongsToMany('App\Models\Plan', 'resellers_plans')->withPivot('commission_percentage','commission_fixed');
	}
	public function getPlansCommissionsAttribute()
	{
		$plans_commissions = [];

		foreach ($this->plans as $plan) 
		{
			$plans_commissions[$plan->id] = [
				'commission_percentage' => $plan->pivot->commission_percentage,
				'commission_fixed' => $plan->pivot->commission_fixed,
			];
		}

		return $plans_commissions;
	}

	public function sites()
	{
		return $this->hasMany('App\Site');
	}

	public static function saveModel($data, $id = null)
	{
		if ($id)
		{
			$item = self::find($id);
			if ( !$item )
			{
				return false;
			}
			$fields = array_keys(self::$update_validator_fields);
		}
		else
		{
			$item = $item = new self();
			$fields = array_keys(self::$create_validator_fields);
		}

		foreach ($fields as $field)
		{
			// Field not set
			if ( !isset($data[$field]) )
			{
				continue;
			}

			// Plans commissions are saved later
			if ( preg_match('#^plans_commissions#', $field) )
			{
				continue;
			}

			// Empty password, keep old one
			if ( $id && $field == 'password' && empty($data[$field]) )
			{
				continue;
			}

			switch ($field) 
			{
				case 'enabled':
					$value = empty($data[$field]) ? 0 : 1;
					break;
				case 'password':
					$value = bcrypt($data[$field]);
					break;
				default:
					$value = @$data[$field];
			}

			$item->$field = $value;
		}

		$item->save();

		if ( isset($data['plans_commissions']) )
		{
			$item->plans()->sync($data['plans_commissions']);
			$item->save();
		}

		return $item;
	}

	public function scopeEnabled($query)
	{
		$query->where('resellers.enabled',1);
	}

}
