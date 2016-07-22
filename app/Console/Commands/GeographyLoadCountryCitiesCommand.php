<?php namespace App\Console\Commands;

use Illuminate\Console\Command;

class GeographyLoadCountryCitiesCommand extends Command
{
	protected $signature = 'geography:load-cities 
								{ country : The country to load }
							';

	protected $description = 'Load cities and states for a given country';

	public function handle()
	{
		// Get country to process
		$country_code = strtoupper( $this->argument('country') );
		if ( $country_code == 'ES' )
		{
			return $this->error("Spain cannot be updated with geonames info until field geonameid is updated");
		}

		$country = \App\Models\Geography\Country::where('code', $country_code)->first();
		if ( !$country )
		{
			return $this->error("Unable to find country in DB: {$country_code}");
		}

		// Check download folder
		$download_folder = storage_path("app/downloads/tmp_{$country_code}");
		if ( !is_dir($download_folder) )
		{
			\File::makeDirectory($download_folder, 0775, true);
		}

		// Get remote file (http://download.geonames.org/export/dump/)
		$zip_remote = "http://download.geonames.org/export/dump/{$country_code}.zip";
		$zip_local = "{$download_folder}/{$country_code}.zip";

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
		$target_filepath = "{$download_folder}/{$country_code}.txt";

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
		$filepath = "{$folder}/{$country_code}.txt";

		// Move file to final location
		if ( !copy($target_filepath, $filepath) )
		{
			return $this->error("Unable to move to final file: {$filepath}");
		}

		// Delete temp files
		\File::deleteDirectory($download_folder);

		// Define columns
		$columns = [ 
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

		// Get current states
		$states = $country->states()->lists('id','geonameid')->all();

		// Process new states
		$i = 0; 
		$total = 0;
		$this->info("Processing states");
		$handle = @fopen($filepath, "r");
		if ($handle) 
		{
			while ( ($buffer = fgets($handle, 4096) ) !== false ) 
			{
				// Format line
				$line = @array_combine($columns, explode("\t",$buffer));

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

				$item = $country->states()->create([
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

		//[TODO] Process cities
echo "<pre>";
print_r($states);
echo "</pre>";
die;

	}

}
