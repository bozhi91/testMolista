<?php

namespace App\Marketplaces\Habitaclia;

class Habitaclia extends \App\Marketplaces\XML {

	protected $iso_lang = 'es';

	/**
	 * @return array
	 */
	public function getAttributes() {
		return (new AttributesHandler())->getAttributes();
	}

}
