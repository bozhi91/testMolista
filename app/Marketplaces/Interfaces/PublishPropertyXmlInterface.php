<?php namespace App\Marketplaces\Interfaces;

interface PublishPropertyXmlInterface {

	/**
	* Get a pending async publication on the marketplace.
	*
	* @param  array		$properties
	* @return string	[string] XML with all the properties received in the $properties param
	*/
	public function getPropertiesXML(array $properties);

	/**
	 * Checks if the $property is valid for publishing on the marketplace. Returns array of errors if not valid.
	 *
	 * @param  array  $property
	 * @return boolean|array
	 */
	public function validateProperty(array $property);

}
