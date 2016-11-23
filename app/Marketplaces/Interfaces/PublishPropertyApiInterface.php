<?php

namespace App\Marketplaces\Interfaces;

interface PublishPropertyApiInterface {

	/**
	 * Publish property
	 * 
	 * @param array $property
	 * @return boolean|array
	 */
	public function publishProperty(array $property);
}
