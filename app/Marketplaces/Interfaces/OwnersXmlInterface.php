<?php namespace App\Marketplaces\Interfaces;

interface OwnersXmlInterface {

	/**
	* Get a owners xml.
	*
	* @param  array		$owners
	* @return string	[string] XML with all the owners received in the $owners param
	*/
	public function getOwnersXML(array $owners);

}
