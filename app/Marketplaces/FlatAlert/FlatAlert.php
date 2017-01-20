<?php

namespace App\Marketplaces\FlatAlert;

use App\Marketplaces\FlatAlert\Service;

class FlatAlert extends \App\Marketplaces\API {

	protected $iso_lang = 'es';
	protected $configuration = [
		[
			'block' => 'access_data',
			'fields' => [
				[
					'name' => 'access_token',
					'type' => 'text',
					'required' => true
				],
				[
					'name' => 'access_token_secret',
					'type' => 'text',
					'required' => true
				],
			]
		],
		[
			'block' => 'contact_data',
			'fields' => [
				[
					'name' => 'email',
					'type' => 'text',
					'required' => true
				],
				[
					'name' => 'phone',
					'type' => 'text',
					'required' => false
				],
			]
		],
	];

	/**
	 * @param array $property
	 */
	public function publishProperty(array $property) {
		$mapper = static::getMapper($property, $this->iso_lang, $this->config);
		if ($mapper->valid()) {
			$mapped = $mapper->map();
			/* @var $service Service */
			$service = $this->getService();

			try {
				$exist = $service->checkPropertyExist($mapped['customer_property_id']);
				if($exist) {
					return $service->updateProperty($mapped);
				}

				return $service->createProperty($mapped);
			} catch (RequestException $e) {
				$res = $e->getResponse();
				return $service->formatResponse($res);
			}
		}
	}

}
