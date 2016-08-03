<?php

namespace App\Marketplaces\ReporteInmobiliario;

class Mapper extends \App\Marketplaces\Mapper {

	/**
	 * Maps a Molista item to ReporteInmobiliario format according to:
	 * Contacto andres.avila@reporteinmobiliario.com
	 * 
	 * @return array
	 */
	public function map() {
		$item = $this->item;

		$map = [];

		$map['internal_id'] = $item['id'];
		$map['type'] = $this->getType();
		$map['property_type'] = $this->getPropertyType();
		$map['title'] = $this->translate($item['title']);
		$map['content'] = $this->translate($item['description']);
		$map['price'] = ceil($item['price']);
		$map['currency'] = $item['currency'];
		$map['country_code'] = $item['location']['country'];
		$map['location'] = $item['location']['state'];
		$map['city_area'] = $item['location']['city'];
		$map['region'] = $item['location']['district'];
		$map['street_name'] = $item['location']['address_parts']['street'];
		$map['street_number'] = $item['location']['address_parts']['number'];

		//Optional
		//$map['barter_enabled'] = '';
		//$map['video'] = '';

		if(!empty($item['commercial_enabled'])){
			$map['commercial_enabled'] = $item['commercial_enabled'] ? 'yes' : 'no';
		}
		
		if(!empty($item['professional_enabled'])){
			$map['professional_enabled'] = $item['professional_enabled'] ? 'yes' : 'no';
		}
		
		if (!empty($item['location']['address_parts']['floor'])) {
			$map['floor'] = $item['location']['address_parts']['floor'];
		}

		if (!empty($item['location']['address_parts']['door'])) {
			$map['flat_number'] = $item['location']['address_parts']['door'];
		}

		if (!empty($item['location']['lat'])) {
			$map['latitude'] = $item['location']['lat'];
		}

		if (!empty($item['location']['lng'])) {
			$map['longitude'] = $item['location']['lng'];
		}

		if (!empty($item['construction_year'])) {
			$map['year'] = $item['construction_year'];
		}

		if (!empty($item['rooms'])) {
			$map['rooms'] = $item['rooms'];
		}

		if (!empty($item['bedrooms'])) {
			$map['bedrooms'] = $item['bedrooms'];
		}

		if (!empty($item['baths'])) {
			$map['bathrooms'] = $item['baths'];
		}

		if (!empty($item['expenses'])) {
			$map['expenses'] = $item['expenses'];
		}

		$map['parking'] = !empty($item['features']['parking']) ? 1 : 0;
	
		if (!empty($item['size'])) {
			$map['total_area'] = $this->convertSize($item['size']);
		}

		if (!empty($item['balcony_area'])) {
			$map['balcony_area'] = $this->convertSize($item['balcony_area']);
		}

		if (!empty($item['lot_area'])) {
			$map['lot_area'] = $this->convertSize($item['lot_area']);
		}

		if (!empty($item['covered_area'])) {
			$map['covered_area'] = $this->convertSize($item['covered_area']);
		}

		if (!empty($item['semi_covered_area'])) {
			$map['semi_covered_area'] = $this->convertSize($item['semi_covered_area']);
		}

		if (!empty($item['uncovered_area'])) {
			$map['uncovered_area'] = $this->convertSize($item['uncovered_area']);
		}

		if (!empty($item['basement_area'])) {
			$map['basement_area'] = $this->convertSize($item['basement_area']);
		}

		if (!empty($item['mezzanine_area'])) {
			$map['mezzanine_area'] = $this->convertSize($item['mezzanine_area']);
		}

		if (!empty($item['usable_area'])) {
			$map['usable_area'] = $this->convertSize($item['usable_area']);
		}

		if (!empty($item['buildable_area'])) {
			$map['buildable_area'] = $this->convertSize($item['buildable_area']);
		}

		if (!empty($item['showcase_front'])) {
			$map['showcase_front'] = $item['showcase_front'];
		}

		if (!empty($item['property_disposal'])) {
			$map['property_disposal'] = $item['property_disposal'];
		}

		if (!empty($item['building_condition'])) {
			$map['building_condition'] = $item['building_condition'];
		}

		if (!empty($item['property_condition'])) {
			$map['property_condition'] = $item['property_condition'];
		}

		$map['pictures'] = $this->getImages();

		$map['contact']['contact_email'] = $this->config['email'];
		$map['contact']['contact_name'] = $this->config['name'];
		$map['contact']['contact_lastname'] = $this->config['lastname'];
		$map['contact']['contact_mobile'] = $this->config['phone'];
		//$map['contact']['contact_business_name'] = '';
		//$map['contact']['contact_landphone'] = '';
		//$map['contact']['contact_picture'] = '';
		//
		//$map['extras']['has_water'] = '';
		//$map['extras']['has_drain'] = '';
		//$map['extras']['has_gas'] = '';
		//$map['extras']['has_internet'] = '';
		//$map['extras']['has_electricity'] = '';
		//$map['extras']['has_pavement'] = '';
		//$map['extras']['has_landphone'] = '';
		//$map['extras']['has_cabletv'] = '';
		
		if(!empty($item['features']['pool'])){
			$map['extras']['has_pool'] = 'yes';
		}

		return $map;
	}

	/**
	 * @return boolean
	 */
	public function valid() {
		$data = array_merge($this->item, $this->config);

		$rules = [
			'id' => 'required',
			'type' => 'required',
			'title' => 'required',
			'description' => 'required',
			'price' => 'required',
			'currency' => 'required',
			'location.country' => 'required',
			'location.state' => 'required',
			'location.city' => 'required',
			'location.district' => 'required',
			'location.address_parts.street' => 'required',
			'location.address_parts.number' => 'required',
			'images.0' => 'required',
			'email' => 'required',
			'phone' => 'required',
			'name' => 'required',
			'lastname' => 'required'
		]; //global rules for all property types

		$validator = \Validator::make($data, $rules, []);
		if ($validator->fails()) {
			$this->errors = $validator->errors()->all();
		}

		//Extra validation by property type
		$propertyType = $this->getPropertyType();
		switch ($propertyType) {
			case 1: $this->validApartments();
				break;
			case 2: $this->validCasa();
				break;
			case 9: $this->validLocalComercial();
				break;
			case 5: $this->validRanche();
				break;
			case 6: $this->validTerreno();
				break;
		}

		return empty($this->errors);
	}

	/**
	 * @return boolean
	 */
	protected function validCasa() {
		$rules = [
			'construction_year' => 'required|regex:#\d{4}#',
			'professional_enabled' => 'required',
			'commercial_enabled' => 'required',
			'rooms' => 'required',
			'bedrooms' => 'required',
			'baths' => 'required',
			'property_condition' => 'required',
			'covered_area' => 'required',
			'semi_covered_area' => 'required',
			'expenses' => 'required',
			'uncovered_area' => 'required'
		];

		$validator = \Validator::make($this->item, $rules, []);
		if ($validator->fails()) {
			$casaErrors = $validator->errors()->all();
			$this->errors = array_merge($this->errors, $casaErrors);
		}

		return empty($this->errors);
	}

	/**
	 * @return boolean
	 */
	protected function validRanche() {
		return $this->validCasa();
	}

	/**
	 * @return boolean
	 */
	protected function validTerreno() {
		$rules = [
			'lot_area' => 'required',
			'buildable_area' => 'required'
		];

		$validator = \Validator::make($this->item, $rules, []);
		if ($validator->fails()) {
			$terrenoErrors = $validator->errors()->all();
			$this->errors = array_merge($this->errors, $terrenoErrors);
		}

		return empty($this->errors);
	}

	/**
	 * @return boolean
	 */
	protected function validLocalComercial() {
		$rules = [
			'construction_year' => 'required|regex:#\d{4}#',
			'bedrooms' => 'required',
			'property_condition' => 'required',
			'expenses' => 'required',
			'basement_area' => 'required',
			'mezzanine_area' => 'required',
			'showcase_front' => 'required',
		];

		$validator = \Validator::make($this->item, $rules, []);
		if ($validator->fails()) {
			$localErrors = $validator->errors()->all();
			$this->errors = array_merge($this->errors, $localErrors);
		}

		return empty($this->errors);
	}

	/**
	 * @return boolean
	 */
	protected function validApartments() {
		$rules = [
			'construction_year' => 'required|regex:#\d{4}#',
			'professional_enabled' => 'required',
			'commercial_enabled' => 'required',
			'property_disposal' => 'required',
			'rooms' => 'required',
			'bedrooms' => 'required',
			'baths' => 'required',
			'property_condition' => 'required',
			'building_condition' => 'required',
			'size' => 'required',
			'covered_area' => 'required',
			'semi_covered_area' => 'required',
			'balcony_area' => 'required',
			'expenses' => 'required',
		];

		$validator = \Validator::make($this->item, $rules, []);
		if ($validator->fails()) {
			$apartmentErrors = $validator->errors()->all();
			$this->errors = array_merge($this->errors, $apartmentErrors);
		}

		return empty($this->errors);
	}

	/**
	 * 1 Venta
	 * 2 Alquiler
	 * 3 Alquiler Temporario
	 * 
	 * @return integer
	 */
	protected function getType() {
		return $this->isSale() ? 1 : 2;
	}

	/**
	 * 1 Departamento
	 * 2 Casa
	 * 3 PH (Tipo Casa)
	 * 4 Countries y Barrios Cerrados
	 * 5 Casa Quinta
	 * 6 Terreno
	 * 7 ???????????????????
	 * 8 Galpon
	 * 9 Local Comercial
	 * 10 Otros
	 * 11 Oficina
	 * 12 ??????????????????
	 * 13 Cochera
	 * 13 Otros
	 * 14 Camas Nauticas
	 * 
	 * @return integer
	 */
	protected function getPropertyType() {
		switch ($this->item['type']) {
			case 'house':
			case 'penthouse':
			case 'villa':
			case 'duplex':
				return 2;
			case 'apartment': return 1;
			case 'lot': return 6;
			case 'store': return 9;
			case 'ranche': return 5;
		}
	}

	/**
	 * @param float $size
	 * @return int
	 */
	protected function convertSize($size) {
		switch ($this->item['size_unit']) {
			case 'sqm': return round($size);
			case 'sqf': return round(($size * 0.092903));
		}
	}

	/**
	 * @return array
	 */
	protected function getImages() {
		$pictures = [];
		foreach ($this->item['images'] as $i => $image) {
			$pictures['picture_url_' . ++$i] = $image;
		}
		return $pictures;
	}

}
