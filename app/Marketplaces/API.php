<?php namespace App\Marketplaces;

use App\Marketplaces\Interfaces\PublishPropertyApiInterface;

abstract class API extends Base implements PublishPropertyApiInterface {
		
	public function publishProperties(array $properties) {
		return [];
	}
	
	public function validateProperty(array $property) {
		return true;
	}
    
}