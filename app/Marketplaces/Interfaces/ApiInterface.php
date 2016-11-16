<?php namespace App\Marketplaces\Interfaces;

interface ApiInterface {

	/**
	 * 
	 */
	public function getProperty();
	
	/**
	 * @param array $property
	 */
	public function publishProperty(array $property);
	
	/**
	 * @param array $property
	 */
	public function updateProperty(array $property);
	
	/**
	 * @param array $property
	 */
	public function unpublishProperty(array $property);
	
}
