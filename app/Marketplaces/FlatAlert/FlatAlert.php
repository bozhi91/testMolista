<?php

namespace App\Marketplaces\FlatAlert;

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
					'required' => false
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
	 * @return array
	 */
	public function publishProperty(array $property) {
		$service = new Service($this->config);
		
		
		
		
		
		/*foreach ($properties as $p) {
			$mapper = static::getMapper($p, $this->iso_lang, $this->config);
			if ($mapper->valid()) {
				$mapped = $mapper->map();
				$service = $this->getService();
				
				
				
				
				//Execute job
				(new PublishPropertyApi($service, $mapped))->handle();
			}
		}*/
	}

	
}
