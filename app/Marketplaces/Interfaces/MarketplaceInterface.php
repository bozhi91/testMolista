<?php namespace App\Marketplaces\Interfaces;

interface MarketplaceInterface {

	/**
	 * Checks if the $property is valid for publishing on the marketplace. Returns array of errors if not valid.
	 *
	 * @param  array  $property
	 * @return boolean|array
	 */
	public function validateProperty(array $property);

}
