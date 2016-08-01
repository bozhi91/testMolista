<?php

namespace App\Marketplaces\ReporteInmobiliario;

class ReporteInmobiliario extends \App\Marketplaces\XML {

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
				[
					'name' => 'lastname',
					'type' => 'text',
					'required' => true
				],
			]
		]
	];
	
	
	/**
	 * @var array
	 */
	public static $currencies = [
		'USD', 'ARS', 'UYU', 'EUR', 'BRL'
	];

	/**
	 * @var array
	 */
	public static $property_disposals = [
		'front', 'back', 'internal'
	];

	/**
	 * @var array 
	 */
	public static $conditions = [
		'excelent', 'very_good', 'good', 'modderate', 'poor'
	];

	/**
	 * @var array
	 */
	public static $countries = [
		'AR', 'US', 'UY', 'BR', 'ES'
	];

}
