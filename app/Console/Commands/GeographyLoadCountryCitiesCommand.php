<?php namespace App\Console\Commands;

use Illuminate\Console\Command;

class GeographyLoadCountryCitiesCommand extends Command
{
	protected $signature = 'geography:load-cities 
								{ country : The country to load }
							';

	protected $description = 'Load cities and states for a given country';

	protected $filepath;

	protected $country;

	protected $columns = [ 
		'geonameid', // integer id of record in geonames database
		'name', // name of geographical point (utf8) varchar(200)
		'asciiname', // name of geographical point in plain ascii characters, varchar(200)
		'alternatenames', // alternatenames, comma separated, ascii names automatically transliterated, convenience attribute from alternatename table, varchar(10000)
		'latitude', // latitude in decimal degrees (wgs84)
		'longitude', // longitude in decimal degrees (wgs84)
		'feature_class', // see http://www.geonames.org/export/codes.html, char(1)
		'feature_code', // see http://www.geonames.org/export/codes.html, varchar(10)
		'country_code', // ISO-3166 2-letter country code, 2 characters
		'cc2', // alternate country codes, comma separated, ISO-3166 2-letter country code, 200 characters
		'admin1_code', // fipscode (subject to change to iso code), see exceptions below, see file admin1Codes.txt for display names of this code; varchar(20)
		'admin2_code', // code for the second administrative division, a county in the US, see file admin2Codes.txt; varchar(80) 
		'admin3_code', // code for third level administrative division, varchar(20)
		'admin4_code', // code for fourth level administrative division, varchar(20)
		'population', // bigint (8 byte int) 
		'elevation', // in meters, integer
		'dem', // digital elevation model, srtm3 or gtopo30, average elevation of 3''x3'' (ca 90mx90m) or 30''x30'' (ca 900mx900m) area in meters, integer. srtm processed by cgiar/ciat.
		'timezone', // the timezone id (see file timeZone.txt) varchar(40)
		'modification', // 
	];

	// Allowed feature codes
	protected $cities_codes = [
			'PPL' => 'populated place (a city, town, village, or other agglomeration of buildings where people live and work)',
			'PPLA' => 'seat of a first-order administrative division (seat of a first-order administrative division (PPLC takes precedence over PPLA))',
			'PPLA2' => 'seat of a second-order administrative division',
			'PPLA3' => 'seat of a third-order administrative division',
			'PPLA4' => 'seat of a fourth-order administrative division',
			'PPLC' => 'capital of a political entity',
			// 'PPLCH' => 'historical capital of a political entity (a former capital of a political entity)',
			// 'PPLF' => 'farm village (a populated place where the population is largely engaged in agricultural activities)',
			// 'PPLG' => 'seat of government of a political entity',
			// 'PPLH' => 'historical populated place (a populated place that no longer exists)',
			// 'PPLL' => 'populated locality (an area similar to a locality but with a small group of dwellings or other buildings)',
			// 'PPLQ' => 'abandoned populated place',
			// 'PPLR' => 'religious populated place (a populated place whose population is largely engaged in religious occupations)',
			'PPLS' => 'populated places (cities, towns, villages, or other agglomerations of buildings where people live and work)',
			// 'PPLW' => 'destroyed populated place (a village, town or city destroyed by a natural disaster, or by war)',
			// 'PPLX' => 'section of populated place',
			// 'STLMT' => 'israeli settlement',
		];

	public function handle()
	{
		// Get country to process
		$country_code = strtoupper( $this->argument('country') );
		if ( $country_code == 'ES' )
		{
			return $this->error("Spain cannot be updated with geonames info until field geonameid is updated");
		}

		$this->country = \App\Models\Geography\Country::where('code', $country_code)->first();
		if ( !$this->country )
		{
			return $this->error("Unable to find country in DB: {$country_code}");
		}

		// Check download folder
		$download_folder = storage_path("app/downloads/tmp_{$this->country->code}");
		if ( !is_dir($download_folder) )
		{
			\File::makeDirectory($download_folder, 0775, true);
		}

		// Get remote file (http://download.geonames.org/export/dump/)
		$zip_remote = "http://download.geonames.org/export/dump/{$this->country->code}.zip";
		$zip_local = "{$download_folder}/{$this->country->code}.zip";
		$this->info("Getting remote file: {$zip_remote}");
		if ( !copy($zip_remote, $zip_local) )
		{
			return $this->error("Unable to download remote file: {$zip_remote}");
		}

		// Extract zip file
		$this->info("Extracting local file: {$zip_local}");
		$zip = new \ZipArchive;
		if ( $zip->open($zip_local) !== true ) 
		{
			return $this->error("Unable to open local zip file: {$zip_local}");
		}
		// Extract
		$zip->extractTo($download_folder);
		$zip->close();

		// Check target file
		$target_filepath = "{$download_folder}/{$this->country->code}.txt";

		// Check file exists
		if ( !file_exists($target_filepath) )
		{
			return $this->error("Unziped file does not exist: {$target_filepath}");
		}

		if ( !is_readable($target_filepath) )
		{
			return $this->error("Unziped file is not readable: {$target_filepath}");
		}

		// Check final location folder
		$folder = storage_path("app/downloads/geography");
		if ( !is_dir($folder) )
		{
			\File::makeDirectory($folder, 0775, true);
		}

		// Define final location
		$this->filepath = "{$folder}/{$this->country->code}.txt";

		// Move file to final location
		if ( !copy($target_filepath, $this->filepath) )
		{
			return $this->error("Unable to move to final file: {$this->filepath}");
		}

		// Delete temp files
		\File::deleteDirectory($download_folder);

		// Load states
		$this->loadStates();

		// Load cities
		$this->loadCities();

		$this->info("Cities and states loaded for country {$this->country->code}");
	}

	protected function loadStates()
	{
		$this->info("Processing states");

		// Set current states
		$states = $this->country->states()->lists('states.id','.states.geonameid')->all();

		$i = 0; 
		$total = 0;

		// Process new states
		$handle = @fopen($this->filepath, "r");
		if ($handle) 
		{
			while ( ($buffer = fgets($handle, 4096) ) !== false ) 
			{
				$i++;

				// Format line
				$line = @array_combine($this->columns, explode("\t",$buffer));

				// Not an administrative item
				if ( $line['feature_class'] != 'A' )
				{
					continue;
				}

				// Not a state
				if ( $line['feature_code'] != 'ADM1' )
				{
					continue;
				}

				// Check if exists
				if ( @$states[$line['geonameid']] )
				{
					continue;
				}

				$item = $this->country->states()->create([
					'geonameid' => $line['geonameid'],
					'code' => $line['admin1_code'],
					'name' => $line['name'],
				]);

				$states[$line['geonameid']] = $item->id;

				$total++;
			}
			fclose($handle);
		}

		$this->info("\tTotal created: {$total}");
	}

	protected function loadCities()
	{
		$this->info("Processing cities");

		// Set current cities
		$cities = $this->country->cities()->lists('cities.id','cities.geonameid')->all();

		// Get current states
		$states = $this->country->states->keyBy('code');

		$i = 0; 
		$total = 0;

		// Process new states
		$handle = @fopen($this->filepath, "r");
		if ($handle) 
		{
			while ( ($buffer = fgets($handle, 4096) ) !== false ) 
			{
				$i++;

				// Format line
				$line = @array_combine($this->columns, explode("\t",$buffer));

				// Not a place
				if ( $line['feature_class'] != 'P' )
				{
					continue;
				}

				// Not a city, town, village, etc
				if ( !isset($line['feature_code'], $this->cities_codes) )
				{
					continue;
				}

				// Check if state exists
				$state = @$states[$line['admin1_code']];
				if ( !$state )
				{
					continue;
				}

				// Check if exists
				if ( @$cities[$line['geonameid']] )
				{
					continue;
				}

				// Check if a city with the same name exists in the same state
				if ( $state->cities()->where('name',$line['name'])->count() > 0 )
				{
					continue;
				}

				$item = $state->cities()->create([
					'geonameid' => $line['geonameid'],
					'name' => $line['name'],
				]);

				$cities[$line['geonameid']] = $item->id;

				$total++;
			}

			fclose($handle);
		}

		$this->info("\tTotal created: {$total}");
	}

}
