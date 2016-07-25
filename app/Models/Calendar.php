<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\ValidatorTrait;

class Calendar extends Model
{
	use SoftDeletes;
	use ValidatorTrait;

	protected $guarded = [];

	protected $casts = [
		'data' => 'array',
	];

	protected $dates = ['deleted_at','start_time','end_time'];

	protected static $create_validator_fields = [
		'user_id' => 'required|exists:users,id',
		'site_id' => 'required|exists:sites,id',
		'property_id' => 'exists:properties,id',
		'customer_id' => 'exists:customers,id',
		'type' => 'required',
		'status' => '',
		'title' => 'required|string',
		'comments' => 'string',
		'data' => 'array',
		'start_time' => 'required|date_format:"Y-m-d H:i"',
		'end_time' => 'required|date_format:"Y-m-d H:i"',
	];

	protected static $update_validator_fields = [
		'user_id' => 'required|exists:users,id',
		'property_id' => 'exists:properties,id',
		'customer_id' => 'exists:customers,id',
		'type' => 'required',
		'status' => '',
		'title' => 'required|string',
		'comments' => 'string',
		'data' => 'array',
		'start_time' => 'required|date_format:"Y-m-d H:i"',
		'end_time' => 'required|date_format:"Y-m-d H:i"',
	];

	protected static $types = [
		'visit',
		'catch',
		'interview',
	];


	public function site()
	{
		return $this->belongsTo('App\Site');
	}

	public function user()
	{
		return $this->belongsTo('App\User');
	}

	public function property()
	{
		return $this->belongsTo('App\Property')->with('infocurrency')->withTranslations();
	}

	public function customer()
	{
		return $this->belongsTo('App\Models\Site\Customer');
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
			$item = new \App\Models\Calendar;
			$fields = array_keys(self::$create_validator_fields);
		}

		foreach ($fields as $field)
		{
			switch ($field) 
			{
				case 'user_id':
				case 'site_id':
				case 'property_id':
				case 'customer_id':
					$item->$field = empty($data[$field]) ? null : @$data[$field];
					break;
				case 'start_time':
				case 'end_time':
					$item->$field = empty($data[$field]) ? '' : $data[$field] . ':00';
					break;
				case 'title':
				case 'comments':
					$item->$field = empty($data[$field]) ? '' : sanitize($data[$field]);
					break;
				default:
					$item->$field = @$data[$field];
			}
		}

		$item->save();

		return $item;
	}

	public function scopeWithStatus($query, $status)
	{
		return $query->where("{$this->getTable()}.status", $status);
	}

	public static function getTypeOptions()
	{
		$types = [];

		foreach (self::$types as $type)
		{
			$types[$type] = trans("account/calendar.reference.type.{$type}");
		}

		return $types;
	}

}
