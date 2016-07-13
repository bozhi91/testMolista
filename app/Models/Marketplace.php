<?php namespace App\Models;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\ValidatorTrait;

class Marketplace extends \App\TranslatableModel
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

	use Translatable;
	public $translatedAttributes = ['instructions'];

	use ValidatorTrait;
	protected static $create_validator_fields = [
		'code' => 'required|unique:marketplaces,code',
		'class_path' => 'required|string',
		'name' => 'required|string',
		'country_id' => 'integer|exists:countries,id',
		'configuration' => 'array',
		'requires_contact' => 'boolean',
		'enabled' => 'boolean',
		'logo' => 'image',
		'url' => 'url',
	];
	protected static $update_validator_fields = [
		'class_path' => 'required|string',
		'name' => 'required|string',
		'country_id' => 'integer|exists:countries,id',
		'configuration' => 'array',
		'enabled' => 'boolean',
		'requires_contact' => 'boolean',
		'logo' => 'image',
		'url' => 'url',
	];

	protected $guarded = [];

	protected $casts = [
		'configuration' => 'array',
	];

	public function country()
	{
		return $this->belongsTo('App\Models\Geography\Country')->withTranslations();
	}

    public function properties()
    {
        $instance = new \App\Property;
        $query = $instance->newQuery();

        return new \App\Relations\BelongsToManyOrToAll($query, $this, 'properties_marketplaces', 'marketplace_id', 'property_id', 'export_to_all', 1);
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
			$item = new \App\Models\Marketplace;
			$fields = array_keys(self::$create_validator_fields);
		}

		foreach ($fields as $field)
		{
			switch ($field)
			{
				case 'logo':
					break;
				case 'country_id':
					$item->$field = empty($data[$field]) ? null : $data[$field];
					break;
				default:
					$item->$field = @$data[$field];
			}
		}

		$translatable_fields = ['instructions'];

		foreach ($translatable_fields as $field)
		{
			if ( empty($data['i18n'][$field]) || !is_array($data['i18n'][$field]) )
			{
				continue;
			}

			foreach ($data['i18n'][$field] as $locale => $value)
			{
				$item->translateOrNew($locale)->$field = $value;
			}
		}


		$item->save();

		return $item;
	}
	public static function saveLogo($item,$request)
	{
		// Delete old logo
		if ( $item->logo )
		{
			@unlink( public_path("marketplaces/{$item->logo}") );
		}

		// Move new logo
		$item->logo = $request->file('logo')->getClientOriginalName();
		while ( file_exists( public_path("marketplaces/{$item->logo}") ) )
		{
			$item->logo = uniqid() . '_' . $request->file('logo')->getClientOriginalName();
		}

		$request->file('logo')->move( public_path('marketplaces'), $item->logo );

		return $item->save();
	}

	public function getAdditionalConfigurationAttribute()
	{
		$additional_configuration = [];

		if ( @$this->configuration['xml_owners'] )
		{
			$additional_configuration['xml_owners'] = true;
		}

        // Recuperar configuración del marketplace
        $adm = new $this->class_path;
        $config = $adm->getMarketplaceConfiguration();
        if (!empty($config))
        {
            $additional_configuration['configuration'] = $config;
        }

		return $additional_configuration;
	}

	public function scopeEnabled($query)
	{
		return $query->where("{$this->getTable()}.enabled", 1);
	}

	public function scopeWithSiteProperties($query,$site_id)
	{
		$query->with([ 'properties' => function($query) use ($site_id) {
			$query->ofSite($site_id);
		}]);
	}

	public function scopeWithSiteConfiguration($query,$site_id)
	{
		$query
			->select('*')
			->leftJoin('sites_marketplaces', function($join) use ($site_id)
			{
				$join->on('marketplaces.id', '=', 'sites_marketplaces.marketplace_id');
				$join->on('sites_marketplaces.site_id', '=', \DB::raw($site_id) );
			})
			->addSelect( \DB::raw('sites_marketplaces.`marketplace_enabled`') )
			->addSelect( \DB::raw('sites_marketplaces.`marketplace_configuration`') )
			->addSelect( \DB::raw('sites_marketplaces.`marketplace_maxproperties`') )
			;
	}

}
