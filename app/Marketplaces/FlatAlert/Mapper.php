<?php

namespace App\Marketplaces\FlatAlert;

class Mapper extends \App\Marketplaces\Mapper {

	/**
	 * @return array
	 */
	public function map() {
		$item = $this->item;
		
		$map = [];

		$map['customer_property_id'] = $item['id'];
		$map['type'] = $this->getType();
		$map['mode'] = '';
		$map['price'] = '400';
		$map['currency'] = '';
		
		$map['address'] = '';
		$map['location'] = '';
		
		$map['features']['area'] = '';
		$map['features']['unit'] = '';
		$map['features']['bedrooms'] = '';
		$map['features']['bathrooms'] = '';
		$map['features']['balcony'] = '';
		$map['features']['elevator'] = '';
		$map['features']['furnished'] = '';
		$map['features']['equiped'] = '';

		$map['energy_certification']['description'] = 'N';
		
		
		
		/*[
			'customer_property_id' => 'blabla',
			'type' => '1',
			'mode' => '1',
			'price' => '400',
			//'currency' => '€',
			//'address' => '',
			'location' => [
				'latitude' => 41.37358080,
				'longitude' => 2.16367380,
			],
			'features' => [
				'area' => 123,
				'unit' => 1,
				'bedrooms' => 3,
				'bathrooms' => 4,
				'balcony' => true,
				'elevator' => true,
				'furnished' => true,
				'equiped' => true,
			],
			'energy_certification' => [
				'description' => 'N'
			],
			'images' => [
				[
					'url' => 'http://www.carinoproperties.com/wordpress/wp-content/uploads/2014/11/1414822499.jpg',
					'principal' => true,
				]
			],
			'plans' => [],
			'status' => 1,
			'contactEmail' => 'vd@incubout.com',
			'contactPhone' => '',
		];*/


		return $map;
	}

	/**
	 * @return boolean
	 */
	public function valid() {
		return true;
	}

	
	/**
	 * Piso -> 1
	 * Casa -> 2
	 * Local -> 3
	 * Parking -> 4
	 * Solar/Parcela -> 5
	 * Oficina -> 6
	 */
	protected function getType(){
		switch ($this->item['type']) {
			case 'flat': 
			case 'duplex':
			case 'apartment':
				return '1';
			case 'villa':
			case 'penthouse':
			case 'house':
			case 'chalet':
			case 'farmhouse':
			case 'bungalow':
				return '2';
				
			
				
			/*case 'ranche':
			case 'state':
				return [5, 'Terrenos y Solares', 2, 'Finca rústica'];
			case 'hotel':
			case 'aparthotel':
				return [10, 'Negocio', 4, 'Hotel'];
			case 'building': return [8, 'Inversiones', 3, 'Edificios'];
			case 'lot': return [5, 'Terrenos y Solares', 1, 'Terreno residencial'];
			case 'store': return [3, 'Local', 1, 'Local Comercial'];
			case 'industrial': return [4, 'Industrial', 1, 'Nave Industrial'];
			
			
			case 'house':
			case 'penthouse':
			case 'villa':
			case 'duplex':
			case 'farmhouse':
			case 'chalet':
				return $this->isRent() ? 'Casas en alquiler' : 'Casas en venta';
			case 'industrial':
				return 'Oficinas y locales';
			case 'lot':
			case 'state':
				return 'Terrenos y solares';
			case 'store':
				return 'Trasteros y garajes';
			case 'hotel':
			case 'aparthotel':
			case 'bungalow':
				return 'Alquiler vacacional';
			default:
				return $this->isRent() ? 'Pisos en alquiler' : 'Pisos en venta';*/
		}
	}
	
}
