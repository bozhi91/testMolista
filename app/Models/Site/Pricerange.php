<?php namespace App\Models\Site;

use \App\TranslatableModel;
use App\Traits\ValidatorTrait;

class Pricerange extends TranslatableModel
{
	use ValidatorTrait;

	public $translatedAttributes = [ 'title' ];
	public $translationForeignKey = 'site_pricerange_id';

    protected $table = 'sites_priceranges';

	protected $guarded = [];

	protected static $create_validator_fields = [
		'site_id' => 'required|exists:sites,id',
		'type' => 'required|in:rent,sale',
		'from' => 'integer|min:0',
		'till' => 'integer|min:0',
		'i18n.title' => 'required|array',
	];

	protected static $update_validator_fields = [
		'from' => 'integer|min:0',
		'till' => 'integer|min:0',
		'i18n.title' => 'required|array',
	];

	public function site()
	{
		return $this->belongsTo('App\Site');
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
			$item = new \App\Models\Site\Pricerange();

			// Set position
			$item->position = intval(self::where('site_id', $data['site_id'])->where('type',$data['type'])->max('position')) + 1;

			$fields = array_keys(self::$create_validator_fields);
		}

		foreach ($fields as $field)
		{
			switch ($field) 
			{
				case 'i18n.title':
					break;
				case 'from':
				case 'till':
					$value = @intval($data[$field]);
					$item->$field = $value ? $value : null;
					break;
				default:
					$item->$field = @$data[$field];
			}
		}

		// Save i18n
		foreach ($data['i18n']['title'] as $locale => $value)
		{
			$item->translateOrNew($locale)->title = sanitize( $value );
		}

		// Save changes
		$item->save();


		return $item;
	}

}
