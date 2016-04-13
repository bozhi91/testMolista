<?php namespace App\Autotranslate;

class Base {

	static public function translate($from, $to, $text_to_translate)
	{
		// The same conversion??? Nothing to do here...
		if ($from == $to)
		{
			return $text_to_translate;
		}

		// Get translation
		$translator = new \App\Autotranslate\MicrosoftTranslator();
		$translator->translate($from, $to, $text_to_translate);
		$response = @json_decode($translator->response->jsonResponse);

		// No response
		if (!$response) return '';

		// No status
		if (!isset($response->status)) return '';

		// Status is not success
		if ($response->status != 'SUCCESS') return '';

		// No translation
		if (empty($response->translation)) return '';

		// Set translation
		$translation = $response->translation;

		$translation = strip_tags($translation);

		return $translation;
	}

}
