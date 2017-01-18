<?php namespace App\Jobs;

use App\Models\Site\Import;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ImportSiteProperties extends Job implements ShouldQueue
{

	use InteractsWithQueue, SerializesModels;

	protected $site;
	protected $import;
	protected $user_id;

	protected $version;
	protected $version_def;
	protected $column_keys;
	protected $insert_fields;
	protected $validator_fields;

	protected $line;
	protected $item;

	protected $countries = [];
	protected $states = [];
	protected $cities = [];

	protected $errors = [];
	protected $messages = [];

	public function __construct(Import $import, $user_id)
	{
		$this->import = $import;
		$this->site = $import->site;

		$this->user_id = $user_id;
	}

	public function handle()
	{
		set_time_limit(0);

		$import = $this->import;

		$import->processing();

		$filepath = storage_path($import->filename);

		// Check file
		if ( !file_exists($filepath) || !is_readable($filepath) )
		{
			return $import->error("Error reading the file (could not be found or is not readable)");
		}

		$delimiter = ';';
		$row_length = 2048;
		$filepointer = fopen($filepath, 'rt');
		if ( !$filepointer )
		{
			return $import->error("Error reading the file (unable to open)");
		}

		// Get version
		$this->version = false;
		$item = fgetcsv($filepointer, $row_length, $delimiter);
		if ( @preg_match('#^Version=(.*)$#', $item[0], $matches) )
		{
			$this->version = trim($matches[1]);
		}

		if ( !$this->version || !in_array($this->version, \App\Models\Site\Import::versionOptions()) )
		{
			return $import->error("Error reading the file (version could not be detected). Make sure that the dilimiter for the CSV file is ;");
		}

		// Update version
		$import->update([ 'version' => $this->version ]);

		// Get column_keys
		$this->column_keys = fgetcsv($filepointer, $row_length, $delimiter);
		if ( !$this->column_keys || !is_array($this->column_keys) )
		{
			return $import->error("Error reading the file (invalid headers)");
		}

		// Set version
		$this->initVersion();

		// Skip titles
		$titles = fgetcsv($filepointer, $row_length, $delimiter);

		// Counters
		$total_processed = 0;
		$total_created = 0;

		$this->item = true;
		$this->line = 0;
		while ( $this->item )
		{
			$this->item = fgetcsv($filepointer, $row_length, $delimiter);
			if ( !$this->item || !array_filter($this->item) )
			{
				break;
			}

			$total_processed++;

			if ( $this->saveItem() )
			{
				$total_created++;
			}

			$this->line++;
		}

		if ( $this->errors )
		{
			return $import->error($this->errors);
		}

		$messages = [ ( $total_created == 1 ) ? '1 property created' : number_format($total_created,0,',','.') . ' properties created' ];

		return $import->complete([ 'messages' => $messages ]);
	}

	protected function saveItem()
	{
		$this->prepareItem();

		if ( !$this->validateItem() )
		{
			return false;
		}

		// Properties limits
		if ( $this->site->property_limit_remaining < 1 )
		{
			$this->item['enabled'] = 0;
		}

		// Prepare lat/lng
		$this->checkItemGeolocation();

		$property = $this->site->properties()->create([
			'enabled' => 0,
			'publisher_id' => $this->user_id ? $this->user_id : null,
			'published_at' => date('Y-m-d'),
		]);

		// Main data
		foreach ($this->insert_fields as $field => $def)
		{
			$def = explode('|', $def);

			if ( in_array('array', $def) )
			{
				continue;
			}

			if ( in_array('boolean', $def) )
			{
				$property->$field = @$this->item[$field] ? 1 : 0;
			}
			elseif ( in_array($field, [ 'country_id','territory_id','state_id','city_id','construction_year','details' ]) )
			{
				$property->$field = @$this->item[$field] ? $this->item[$field] : null;
			}
			else
			{
				if ( @$this->item[$field] )
				{
					$property->$field = sanitize( $this->item[$field] );

				}
			}
		}

		// Title && subtitle
		$property->translateOrNew(fallback_lang())->title = @sanitize( $this->item['title'] );
		$property->translateOrNew(fallback_lang())->description = @sanitize( $this->item['description'] );

		if ( @$this->item['services'] )
		{
			foreach ($this->item['services'] as $service_id)
			{
				$property->services()->attach($service_id);
			}
		}

		// Add currency
		$property->currency = $this->site->site_currency;

		$property->save();

		// Image
		if ( @$this->item['image'] )
		{
			// Images directory
			$dirpath = $property->image_path;

			// Prepare filename
			$filename = $ofilename = pathinfo($this->item['image'], PATHINFO_BASENAME);
			while ( file_exists("{$dirpath}/{$filename}") )
			{
				$filename = uniqid()."_{$ofilename}";
			}

			if ( copy($this->item['image'], "{$dirpath}/{$filename}") )
			{
				$new_image = $property->images()->create([
					'image' => $filename,
					'position' => 0,
					'default' => 1,
				]);
			}
		}

		return true;
	}

	protected function checkItemGeolocation()
	{
		if ( @$this->item['lat'] && @$this->item['lng'] )
		{
			return true;
		}

		$address = implode(', ', array_filter([
			@$this->item['address'],
			@$this->item['zipcode'],
			@$this->item['city'],
			@$this->item['state'],
			@$this->item['country'],
		]));

		$response = \Geocoder::geocode('json', [
			'address' => $address,
		]);

		$response = json_decode($response);
		if ( @$response->status == 'OK' )
		{
			if ( @$response->results && is_array($response->results) )
			{
				$first = array_shift($response->results);
				$this->item['lat']	= $first->geometry->location->lat;
				$this->item['lng']	= $first->geometry->location->lng;
				return true;
			}
		}

		$this->item['lat']	= config('app.lat_default');
		$this->item['lng']	= config('app.lng_default');

		return false;
	}

	protected function validateItem()
	{
		// General validation
		$validator = \Validator::make($this->item, $this->validator_fields);

		// Validate location
		$validator->after(function($validator) {
			$validator = $this->validateItemCountry($validator);
			$validator = $this->validateItemState($validator);
			$validator = $this->validateItemCity($validator);
		});

		if ($validator->fails())
		{
			$errors = $validator->errors()->all();
			if ( count($errors) > 1 )
			{
				$this->addError( "Line {$this->line}:" . '<ul><li>' . implode('</li><li>', $validator->errors()->all()) . '</li></ul>');
			}
			else
			{
				$this->addError( "Line {$this->line}: " . array_shift($errors));

			}
			return false;
		}


		return true;
	}

	protected function validateItemCountry($validator)
	{
		if ( @$this->item['country'] )
		{
			if ( !array_key_exists($this->item['country'], $this->countries) )
			{
				$country = \App\Models\Geography\Country::whereTranslationLike('name', "{$this->item['country']}")->first();
				$this->countries[$this->item['country']] = $country ? $country->id : false;
			}

			if ( $this->countries[$this->item['country']] )
			{
				$this->item['country_id'] = $this->countries[$this->item['country']];
			}
			else
			{
				$validator->errors()->add('country', "The country is invalid");
			}
		}

		return $validator;
	}

	protected function validateItemState($validator)
	{
		if ( @$this->item['country_id'] && @$this->item['state'] )
		{
			if ( !array_key_exists($this->item['state'], $this->states) )
			{
				$state = \App\Models\Geography\State::where('country_id', $this->item['country_id'])->where('name', $this->item['state'])->first();
				$this->states[$this->item['state']] = $state ? $state->id : false;
			}

			if ( $this->states[$this->item['state']] )
			{
				$this->item['state_id'] = $this->states[$this->item['state']];
			}
			else
			{
				$validator->errors()->add('state', "The state is invalid");
			}
		}

		return $validator;
	}

	protected function validateItemCity($validator)
	{
		if ( @$this->item['state_id'] && @$this->item['city'] )
		{
			if ( !array_key_exists($this->item['city'], $this->cities) )
			{
				$city = \App\Models\Geography\City::where('state_id', $this->item['state_id'])->where('name', $this->item['city'])->first();
				$this->cities[$this->item['city']] = $city ? $city->id : false;
			}

			if ( $this->cities[$this->item['city']] )
			{
				$this->item['city_id'] = $this->cities[$this->item['city']];
			}
			else
			{
				$validator->errors()->add('state', "The city is invalid");
			}
		}

		return $validator;
	}

	protected function prepareItem()
	{
		// Data
		$data = [];
		foreach ($this->column_keys as $key => $field)
		{
			switch ($key)
			{
				default:
					$value = @utf8_encode($this->item[$key]);
			}

			$data[$field] = $value;
		}

		// Item
		$item = [];
		foreach ($this->version_def as $field => $def)
		{
			$item[$field] = @$data[$field];
		}

		// Fix services
		$item['services'] = [];
		foreach ($item as $field => $value)
		{
			if ( !preg_match('#^service-(\d+)$#', $field, $matches) )
			{
				continue;
			}

			if ($value)
			{
				$item['services'][$matches[1]] = $matches[1];
			}

			unset($item[$field]);
		}

		// Fix case
		foreach (['type', 'mode'] as $f)
		{
			if (!empty($item[$f]))
			{
				$item[$f] = strtolower($item[$f]);
			}
		}

		$this->item = $item;
	}

	protected function initVersion()
	{
		$this->version_def = \App\Models\Site\Import::getColumns($this->version);

		// Unset invalid columns
		foreach ($this->column_keys  as $key => $field)
		{
			if ( array_key_exists($field, $this->version_def) )
			{
				continue;
			}

			unset( $this->column_keys[$key] );
		}

		// Set validator_fields
		$this->validator_fields = [];
		foreach ($this->version_def as $field => $def)
		{
			if ( preg_match('#^service-(\d+)$#', $field, $matches) )
			{
				continue;
			}

			$rules = [];

			if ( @$def['required'] )
			{
				$rules[] = 'required';
			}

			switch ( @$def['type'] )
			{
				case 'boolean':
					$rules[] = 'boolean';
					break;
				case 'dropdown':
					if ( !empty($def['options']) && is_array($def['options']) )
					{
						$rules[] = 'in:'.implode(',',$def['options']);
					}
					break;
				case 'decimal':
					$rules[] = 'numeric';
					break;
				case 'integer':
					$rules[] = 'integer';
					break;

			}

			switch ( $field )
			{
				case 'ref':
					$rules[] = "unique:properties,ref,NULL,id,site_id,{$this->site->id}";
					break;
			}

			$this->validator_fields[$field] = implode('|', array_filter($rules));
		}

		$this->validator_fields['services'] = 'array';
		$this->validator_fields['services.*'] = 'integer|in:'.implode(',', \App\Models\Property\Service::lists('id')->all());

		// Set insert fields
		$fields = $this->validator_fields;
		unset($fields['image'],$fields['services']);
		foreach (['country','territory','state','city'] as $field)
		{
			unset($fields[$field]);
			$fields["{$field}_id"] = '';
		}
		$this->insert_fields = $fields;
	}

	protected function addMessage($message)
	{
		$this->messages[] = $message;
	}

	protected function addError($message)
	{
		$this->errors[] = $message;
	}

}
