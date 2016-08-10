<?php namespace App\Console\Commands;

use Illuminate\Console\Command;

class TranslationsBulkCommand extends Command
{
	protected $signature = 'translations:bulk {language : The language to translate}';

	protected $description = 'Create translations for a given language';

	protected $from = 'en';

	public function handle()
	{
		$language = $this->argument('language');

		// Language is required
		if ( !$language )
		{
			$this->error("Language not defined");
			return false;
		}

		// And must be a valid locale
		if ( \App\Models\Locale::where('locale',$language)->count() < 1 )
		{
			$this->error("Language {$language} does not exist");
			return false;
		}

		// Get current translations
		$translated = \App\Models\TranslationTranslation::where('locale',$language)->where('value','!=','')->whereNotNull('value')->lists('value','translation_id')->all();

		// Set locale
		\App::setLocale($this->from);

		// counter
		$i = 0;

		// Process translations
		foreach (\App\Models\Translation::with('translations')->get() as $item)
		{
			$i++;
			echo "\ritem: {$i}";

			// Empty translation
			if ( !$item->value )
			{
				continue;
			}

			// Already translated
			if ( @$translated[$item->id] )
			{
				continue;
			}

			// Get translation
			$value = \App\Autotranslate\Base::translate($this->from, $language, $item->value);

			// Translation error
			if ( !$value )
			{
				$this->warn("Traslation failed (ID {$item->id})");
				continue;
			}

			// Save translation
			$translation = \App\Models\TranslationTranslation::firstOrCreate([
				'translation_id' => $item->id,
				'locale' => $language,
			]);

			if (!$translation)
			{
				$this->error("Failed to create translation (ID {$item->id})");
				continue;
			}

			$translation->value = \App\Models\Translation::cleanValue($value);
			$translation->save();
		}
		echo "\n";
		$this->info("Total tags translated: ".number_format($i,0,',','.'));

		// Compile translations
		$this->info("Compiling translations");
		$files = \DB::table('translations')
							->selectRaw("DISTINCT(file) as file")
							->orderBy('file')
							->get();
		foreach ($files as $item)
		{
			\App\Models\Translation::compileTranslation($item->file,$language);
		}

	}

}
	