<?php namespace App\Models\Site;

use Illuminate\Database\Eloquent\Model;

use App\Traits\ValidatorTrait;

class UserSignature extends Model
{
	use ValidatorTrait;

    protected $table = 'sites_users_signatures';

	protected $guarded = [];

	protected $casts = [
		'images' => 'array',
	];

	protected static $create_validator_fields = [
		'site_id' => 'required|exists:sites,id',
		'user_id' => 'required|exists:users,id',
		'title' => 'required|string',
		'signature' => 'string',
		'default' => 'boolean',
	];

	protected static $update_validator_fields = [
		'title' => 'required|string',
		'signature' => 'string',
		'default' => 'boolean',
	];

	public function site()
	{
		return $this->belongsTo('App\Site');
	}

	public function user()
	{
		return $this->belongsTo('App\User');
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

			$old_images = $item->images;
		}
		else
		{
			$item = new \App\Models\Site\UserSignature();
			$fields = array_keys(self::$create_validator_fields);
		}

		foreach ($fields as $field)
		{
			switch ($field) 
			{
				case 'title':
					$item->$field = @strip_tags($data[$field]);
					break;
				case 'default':
					$item->$field = @$data[$field] ? 1 : 0;
					break;
				case 'signature':
				default:
					$item->$field = @$data[$field];
			}
		}

		$item->images = @$data['images'];
		if ( !is_array($item->images) )
		{
			$item->images = [];
		}

		$item->save();

		// If default...
		if ( $item->default )
		{
			self::ofSite($item->site_id)->ofUser($item->user_id)->where('id','!=',$item->id)->update([
				'default' => 0,
			]);
		}

		if ( !empty($old_images) )
		{
			foreach ($old_images as $old_image) 
			{
				if ( !in_array($old_image, $item->images) )
				{
					@unlink($old_image);
				}
			}
		}

		return $item;
	}

	public function scopeOfSite($query, $site_id)
	{
		return $query->where("{$this->table}.site_id", $site_id);
	}

	public function scopeOfUser($query, $user_id)
	{
		return $query->where("{$this->table}.user_id", $user_id);
	}

}
