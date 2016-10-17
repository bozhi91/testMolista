<?php

namespace App\Marketplaces\Habitaclia;

class Habitaclia extends \App\Marketplaces\XML {

	protected $iso_lang = 'es';
	protected $configuration = [
		[
			'block' => 'contact_data',
			'fields' => [
				[
					'name' => 'email',
					'type' => 'text',
					'required' => true
				],
			]
		]
	];

	/**
	 * @return array
	 */
	public function getAttributes() {
		return (new AttributesHandler())->getAttributes();
	}

}
