<?php namespace App\Models\Site;

use Illuminate\Database\Eloquent\Model;

class Import extends Model
{
	static protected $version = '2016.0901';

	protected $table = 'sites_imports';

	protected $guarded = [];

	protected $casts = [
		'result' => 'array',
	];

	public function site()
	{
		return $this->belongsTo('App\Site');
	}

	public function setStatus($status)
	{
		$this->status = $status;
		$this->save();
	}

	public function error($messages)
	{
		if (!is_array($messages))
		{
			$messages = [ $messages ];
		}

		$this->result = ['messages' => $messages];

		return $this->setStatus('error');
	}

	public function complete($result = null)
	{
		$this->result = $result;
		return $this->setStatus('completed');
	}

	public function processing()
	{
		return $this->setStatus('processing');
	}

	static public function getColumns($version = false)
	{
		// Set fields
		switch ( static::$version )
		{
			case '2016.0901':
			default:
				$fields = [
					'ref', 'type', 'mode', 
					'enabled',
					'price', 'size', 'rooms', 'baths',
					'ec', 'ec_pending',
					'construction_year',
					'newly_build', 'second_hand', 'new_item', 'opportunity', 'private_owned', 'bank_owned',
					'country', 'state', 'city', 'district', 'address', 'zipcode', 'show_address', 'lat', 'lng',
					'title', 'description',
					'image',
				];
		}

		$the_options = [
			'boolean' => [ 0, 1 ],
			'types' => array_keys(\App\Property::getTypeOptions()),
			'modes' => \App\Property::getModes(),
			'size_unit' => array_keys(\App\Property::getSizeUnitOptions()),
			'ec' => array_keys(\App\Property::getEcOptions()),
		];

		$columns = [];
		foreach ($fields as $field)
		{
			$required = false;
			$type = 'text';
			$options = '';

			// Set title
			switch ($field)
			{
				case 'ec':
					$title = trans('account/properties.energy.certificate');
					break;
				case 'ec_pending':
					$title = trans('account/properties.energy.certificate.pending');
					break;
				case 'new_item':
					$title = trans('account/properties.new.item');
					break;
				default:
					$title = trans("account/properties.{$field}");
			}

			// Set required
			switch ($field)
			{
				case 'ref':
				case 'type':
				case 'mode':
				case 'price':
				case 'size':
				case 'rooms':
				case 'baths':
				case 'country':
				case 'state':
				case 'city':
				case 'title':
					$required = true;
					break;
			}

			// Set type && options
			switch ($field)
			{
				case 'type':
					$type = 'dropdown';
					$options = $the_options['types'];
					break;
				case 'mode':
					$type = 'dropdown';
					$options = $the_options['modes'];
					break;
				case 'ec':
					$type = 'dropdown';
					$options = $the_options['ec'];
					break;
				case 'enabled':
				case 'ec_pending':
				case 'newly_build':
				case 'second_hand':
				case 'new_item':
				case 'opportunity':
				case 'private_owned':
				case 'bank_owned':
				case 'show_address':
					$type = 'boolean';
					$options = $the_options['boolean'];
					break;
				case 'price':
				case 'size':
				case 'lat':
				case 'lng':
					$type = 'decimal';
					break;
				case 'rooms':
				case 'baths':
				case 'construction_year':
					$type = 'integer';
					break;
			}

			$columns[$field] = [
				'title' => $title,
				'required' => $required,
				'type' => $type,
				'options' => $options,
			];
		}

		// Add services
		$services = \App\Models\Property\Service::withTranslations()->orderBy('id')->get();
		foreach ($services as $service)
		{
			$columns["service-{$service->id}"] = [
				'title' => $service->title,
				'required' => false,
				'type' => 'boolean',
				'options' => $the_options['boolean'],
			];

		}

		foreach ($columns as $field => $def)
		{
			if ( is_array($def['options']) )
			{
				sort($def['options']);
				//$def['options'] = implode('|', $def['options']);
				$columns[$field]['options'] = $def['options'];
			}

		}

		return $columns;
	}

	static public function getSampleFileLocation($version)
	{
		$locale = app()->getLocale();
		$dirpath = "sites/imports/";
		$filepath = "{$dirpath}{$version}-{$locale}.csv";

		if ( !file_exists( public_path($filepath) ) )
		{
			$columns = static::getColumns($version);

			$csv = \League\Csv\Writer::createFromFileObject(new \SplTempFileObject());
			$csv->setDelimiter(';');

			// Header
			$csv->insertOne([
				"Version={$version}",
				trans('account/properties.imports.csv.header'),
			]);

			// Keys
			$csv->insertOne( array_keys($columns) );

			// Titles
			$data = [];
			foreach ($columns as $item)
			{
				$data[] = $item['title'] . (empty($item['required']) ? '' : ' *');

			}
			$csv->insertOne( $data );

			if ( !is_dir($dirpath) )
			{
				\File::makeDirectory($dirpath, 0775, true);
			}

			\File::put(public_path($filepath), $csv->__toString());
		}

		return asset($filepath);
	}

	static public function versionCurrent()
	{
		return static::$version;
	}

	static public function versionOptions()
	{
		return [ 
			'2016.0901' => '2016.0901',
		];
	}

}