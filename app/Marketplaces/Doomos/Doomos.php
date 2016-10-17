<?php

namespace App\Marketplaces\Doomos;

use App\Marketplaces\Interfaces\UnifiedXmlInterface;

class Doomos extends \App\Marketplaces\XML implements UnifiedXmlInterface {

	protected $iso_lang = 'es';

	/**
	 * @param array $files
	 * @return array
	 */
	public function getUnifiedXml(array $files) {
		return null;
	}
}
