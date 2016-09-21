<?php

namespace App\Marketplaces\Tuad;

class Mapper extends \App\Marketplaces\Mapper {

	/**
	 * Maps a Molista item to trovit.com format according to:
	 * http://tuad.es/webmasters
	 *
	 * @return array
	 */
	public function map() {
		$item = $this->item;

		$map = [];
		$map['id'] = $item['id'];
		$map['title'] = $this->translate($item['title']);
		$map['content'] = $this->translate($item['description']);
		$map['category'] = $this->getCategory();
		$map['m2'] = $this->getSize();
		$map['rooms'] = $this->item['rooms'];
		$map['bathrooms'] = $this->item['baths'];
		$map['city'] = $this->item['location']['city'];
		$map['region'] = $this->getRegion();
		$map['postcode'] = $this->item['location']['zipcode'];

		$map['email'] = $this->config['email'];
		$map['phone'] = $this->config['phone'];

		if (isset($this->config['name'])) {
			$map['alias'] = $this->config['name'];
		}

		if (isset($this->item['price'])) {
			$map['price'] = ceil($this->item['price']);
		}

		if(!empty($this->item['images'])){
			$map['pictures']['picture'] = $this->getImages();
		}

		return $map;
	}

	/**
	 * @return boolean
	 */
	public function valid()
	{
		$data = array_merge($this->item, $this->config);

		if (in_array($this->item['type'], ['building']))
		{
		    $this->errors []= \Lang::get('validation.type');
		    return false;
		}

		if ($this->isTransfer()) {
			$this->errors []= \Lang::get('validation.transfer');
            return false;
		}

		$rules = [
			'id' => 'required',
			'title' => 'required',
			'description.es' => 'required',
			'type' => 'required',
			'size' => 'required',
			'size_unit' => 'required',
			'rooms' => 'required',
			'baths' => 'required',
			'location.city' => 'required',
			'location.state' => 'required',
			'location.zipcode' => 'required',
			'price' => 'required',
			'email' => 'required',
			'phone' => 'required'
		];

		$validator = \Validator::make($data, $rules, []);
		if ($validator->fails()) {
			$this->errors = $validator->errors()->all();
		}

		return empty($this->errors);
	}

	/**
	 * 	Inmobiliaria > Alquiler vacacional
	 * 	Inmobiliaria > Casas en alquiler
	 * 	Inmobiliaria > Casas en venta
	 * 	Inmobiliaria > Oficinas y locales
	 * 	Inmobiliaria > Pisos compartidos
	 * 	Inmobiliaria > Pisos en alquiler
	 * 	Inmobiliaria > Pisos en venta
	 * 	Inmobiliaria > Terrenos y solares
	 * 	Inmobiliaria > Trámites
	 * 	Inmobiliaria > Trasteros y garajes
	 *
	 * @return string
	 */
	protected function getCategory() {
		switch ($this->item['type']) {
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
				return $this->isRent() ? 'Pisos en alquiler' : 'Pisos en venta';
		}
	}

	/**
	 * @return int
	 */
	protected function getSize() {
		switch ($this->item['size_unit']) {
			case 'sqm': return round($this->item['size']);
			case 'sqf': return round(($this->item['size'] * 0.092903));
		}
	}

	/**
	 * @return string
	 */
	protected function getRegion() {
		return $this->item['location']['state'];
	}

	/**
	 * @return array
	 */
	protected function getImages() {
		$pictures = [];
		foreach ($this->item['images'] as $i => $image) {
			if ($i >= 4) {
				continue; //limit 4 pictures
			}

			$pictures[] = ['picture_url' => $image];
		}
		return $pictures;
	}

}
