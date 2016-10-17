<?php namespace App\Marketplaces\Interfaces;

interface UnifiedXmlInterface {

	/**
	* Get a unified xml file.
	*
	* @param  array		$files
	* @return string	[string] XML with all the properties received in the $files param
	*/
	public function getUnifiedXml(array $files);

}
