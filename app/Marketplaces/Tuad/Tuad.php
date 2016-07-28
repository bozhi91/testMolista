<?php

namespace App\Marketplaces\Tuad;

class Tuad extends \App\Marketplaces\XML {

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
					'type' => 'text'
				],
			]
		]
	];

}
