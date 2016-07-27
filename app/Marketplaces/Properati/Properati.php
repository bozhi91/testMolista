<?php

namespace App\Marketplaces\Properati;

class Properati extends \App\Marketplaces\XML {

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
				[
					'name' => 'phone',
					'type' => 'text',
					'required' => true
				],
				[
					'name' => 'name',
					'type' => 'text',
					'required' => true
				],
			]
		]
	];

}
