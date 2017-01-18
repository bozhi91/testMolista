<?php namespace App\Console\Commands;

use Illuminate\Console\Command;

class TranslationsImportCommand extends Command
{
	protected $signature = 'translations:import
							{file? : The name of the CSV file to be imported (must be stored in storage folder)}';

	protected $description = 'Import translations from a csv file';

	public function handle()
	{
		// File as argument
		$file = $this->argument('file');

		// No file
		if ( !$file )
		{
			// Prompt for file
			$file = $this->ask('Name of the CSV file (must be stored in storage folder)?');
		}

		// No file name
		if ( !$file )
		{
			$this->error("No file name provided");
			exit;
		}

		$filepath = storage_path($file);

		// Check if filepath exists
		if ( !file_exists($filepath) )
		{
			$this->error("File does not exist : {$filepath}");
			exit;
		}

		// Check if filepath exists
		if ( !is_readable($filepath) )
		{
			$this->error("File is not readable : {$filepath}");
			exit;
		}

		$total = 0;
		$columns = [];
		$locales = [];

		// Get enabled locales
		$enabled_locales = \App\Models\Locale::lists('name','locale')->all();

		// Process file
		if ( ($handle = fopen($filepath, "r")) !== FALSE )
		{
			while ( ($data = fgetcsv($handle, 0, ';')) !== FALSE )
			{
				// Set headers
				if ( !$columns )
				{
					// Set columns
					foreach ($data as $key => $field)
					{
						$columns[$field] = $key;
						if ( array_key_exists($field, $enabled_locales) )
						{
							$locales[$field] = $enabled_locales[$field];
						}
					}

					if ( !isset($columns['file']) )
					{
						$this->error("The 'file' column is not defined");
						exit;
					}

					if ( !isset($columns['tag']) )
					{
						$this->error("The 'tag' column is not defined");
						exit;
					}

					if ( count($locales) < 1 )
					{
						$this->error("No valid languages were found");
						exit;
					}

					foreach ($locales as $locale => $locale_name)
					{
						if ( !$this->confirm("Translate to {$locale_name}?") )
						{
							unset($locales[$locale]);
						}
					}

					if ( count($locales) < 1 )
					{
						$this->error("No language was selected");
						exit;
					}

					continue;
				}

				$file = @$data[$columns['file']];
				$tag = @$data[$columns['tag']];

				// Get item
				$item = \App\Models\Translation::where('file', $file)->where('tag', $tag)->first();
				if ( !$item )
				{
					//$this->warn("{$file} -> {$tag}");
					//$this->warn("Translation not found");
					continue;
				}

				// Process locales
				foreach ($locales as $locale => $locale_name)
				{
					// No translation
					$value = @$data[$columns[$locale]];
					if ( !$value )
					{
						//$this->warn("{$file} -> {$tag}");
						//$this->warn("No translation provided for {$locales[$locale]}");
						continue;
					}

					// Save translation
					$translation = \App\Models\TranslationTranslation::firstOrCreate([
						'translation_id' => $item->id,
						'locale' => $locale,
					]);

					if ( !$translation )
					{
						//$this->warn("{$file} -> {$tag}");
						//$this->warn("Unable to create translation");
						continue;
					}

					// Already translated
					if ( $translation->value )
					{
						//$this->warn("{$file} -> {$tag}");
						//$this->warn("Already translated to {$locales[$locale]}");
						continue;
					}

					$translation->value = \App\Models\Translation::cleanValue($value);
					$translation->save();

					$this->info("{$file} -> {$tag} translated to {$locales[$locale]}");
					$total++;
				}
			}
			fclose($handle);
		}
		else
		{
			$this->error("Unable to open the file : {$filepath}");
			exit;
		}

		// Compile translations
		$this->info("Compiling translations");
		$files = \DB::table('translations')
							->selectRaw("DISTINCT(file) as file")
							->orderBy('file')
							->get();
		foreach ($files as $item)
		{
			foreach ($locales as $locale => $locale_name)
			{
				\App\Models\Translation::compileTranslation($item->file,$locale);
			}
		}

		$this->info("Translations imported: {$total}");
	}

}
	