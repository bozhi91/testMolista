<?php namespace App\Autotranslate;

class Base {

	static public function translate($from, $to, $text_to_translate)
	{
		// The same conversion??? Nothing to do here...
		if ($from == $to)
		{
			return $text_to_translate;
		}

		// Google translate
		return \App\Autotranslate\GoogleTranslator::translate($from, $to, $text_to_translate);
	}

}
