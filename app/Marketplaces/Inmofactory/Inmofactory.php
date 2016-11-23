<?php

namespace App\Marketplaces\Inmofactory;

use App\Marketplaces\Inmofactory\Service;

abstract class Inmofactory extends \App\Marketplaces\API {

	protected $configuration = [
		[
			'block' => 'access_data',
			'fields' => [
				[
					'name' => 'username',
					'type' => 'text',
					'required' => true
				],
				[
					'name' => 'password',
					'type' => 'text',
					'required' => true
				],
				[
					'name' => 'agent_id',
					'type' => 'text',
					'required' => true
				]
			]
		],
		[
			'block' => 'contact_data',
			'fields' => [
				[
					'name' => 'email',
					'type' => 'text',
					'required' => false
				],
			]
		],
	];

	/**
	 * @param array $property
	 * @return array
	 */
	public function publishProperty(array $property) {
		$mapper = static::getMapper($property, $this->iso_lang, $this->config);
		if ($mapper->valid()) {
			$mapped = $mapper->map();
			/* @var $service Service */
			$service = $this->getService();

			$result = $service->updateProperty($mapped);
			
			if(!$result[0]){
				$messages = $result[1]['messages'];
				if($messages[0] == 'No existe un inmueble con el externalId informado'){
					$result = $service->createProperty($mapped);
				}
			}
			
			return $result;
		}
	}

}
