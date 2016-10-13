<?php

namespace App\Marketplaces\Interfaces;

interface PublishPropertyApiInterface {

	/**
	 * Publish properties
	 * 
	 * @param array $properties
	 * @return boolean|array
	 */
	public function publishProperties(array $properties);
	
}
