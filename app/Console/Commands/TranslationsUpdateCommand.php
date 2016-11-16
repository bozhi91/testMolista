<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TranslationsUpdateCommand extends Command
{
	protected $signature = 'translations:update
								{--reset= : Delete current translations from database}';

	protected $description = 'Update translations';

	public function handle()
	{
		if ( $this->option('reset') )
		{
			$this->info( "Delete current translations" );
			\DB::table('translations')->whereNotNull('id')->delete();
			\DB::statement('ALTER TABLE `translations_translations` AUTO_INCREMENT=1;');
			\DB::statement('ALTER TABLE `translations` AUTO_INCREMENT=1;');
		}

		$input_path = resource_path('lang_src');

		$languages = [];

		$this->info( "Generate new translations" );

		// Process languages
		foreach (scandir($input_path) as $locale)
		{
			if ( $locale === '.' ) continue;
			if ( $locale === '..' ) continue;
			if ( strlen($locale) != 2 ) continue;

			$locale_path = "{$input_path}/{$locale}";
			if (!is_dir($locale_path)) continue;

			$languages[] = $locale;

			$files = [];
			$this->listFolderFiles($locale_path, $files);

			foreach ($files as $path)
			{
				if (!preg_match('#\.php$#', $path)) continue;

				$file = str_replace("{$locale_path}/", '', $path);
				$file = preg_replace('#\.php$#', '', $file);

				$tags = include($path);
				if ( empty($tags) ) continue;
				if ( !is_array($tags) ) continue;

				$this->saveTags($locale,$file,$tags);
			}
		}

		// Create language files
		$files_langs = [];
		$files = \DB::table('translations')
							->selectRaw("DISTINCT(file) as file")
							->orderBy('file')
							->get();
		foreach ($files as $item)
		{
			foreach ($languages as $locale)
			{
				$files_langs[$locale] = $locale;
				\App\Models\Translation::compileTranslation($item->file,$locale);
			}
		}

		if (!empty($files_langs))
		{
			$this->info( "Generate translation files: ".implode(', ', $files_langs) );
		}
	}

	protected function listFolderFiles($dir, &$files)
	{
		foreach( scandir($dir) as $file )
		{
			if ( $file === '.' ) continue;
			if ( $file === '..' ) continue;

			$path = "{$dir}/{$file}";

			if (is_dir($path))
			{
				$this->listFolderFiles($path, $files);
			}
			else
			{
				$files[] = $path;
			}
		}
	}

	protected function saveTags($locale,$file,$tags)
	{
		// For each tag in file
		foreach ($tags as $tag=>$value)
		{
			// Cheack if tag exists
			$item = \App\Models\Translation::where([
				'file' => $file,
				'tag' => $tag,
			])->first();

			// If new
			if ( !$item)
			{
				// Create it
				$item = \App\Models\Translation::create([
					'file' => $file,
					'tag' => $tag,
				]);
				// Error
				if (!$item)
				{
					$this->error( "\tError creating tag {$file}.{$tag}" );
					continue;
				}

				$this->info( "\tTag {$file}.{$tag} created" );
			}

			// If value is an array
			if ( is_array($value) )
			{
				// For each value
				foreach ($value as $k=>$v)
				{
					// Create tag / values array
					$array_tags = [ "{$tag}.{$k}" => $v ];
					// Save all language tags
					$this->saveTags($locale,$file,$array_tags);
				}
			}
			else
			{
				// Save translation
				$this->saveTranslation($item->id, $locale, $value);
			}
		}
		return true;
	}

	protected function saveTranslation($translation_id,$locale,$value)
	{
		$item = \App\Models\TranslationTranslation::where([
			'translation_id' => $translation_id,
			'locale' => $locale,
		])->first();

		if ($item)
		{
			return false;
		}

		$item = \App\Models\TranslationTranslation::create([
			'translation_id' => $translation_id,
			'locale' => $locale,
		]);
		if (!$item)
		{
			$this->error( "\tError creating translation #{$translation_id}");
			return false;
		}

		$item->value = \App\Models\Translation::cleanValue($value);
		$item->save();

		return true;
	}
}
