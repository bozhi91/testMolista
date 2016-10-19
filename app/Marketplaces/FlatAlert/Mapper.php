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
		$map['mode'] = $this->getMode();
		$map['price'] = number_format($item['price'], 2, '.', '');
		$map['currency'] = $this->getCurrency();

		$location = $item['location'];
		if (!empty($location['lat']) && !empty($location['lng'])) {
			$map['location']['latitude'] = $location['lat'];
			$map['location']['longitude'] = $location['lng'];
		} else {
			$map['address']['type'] = $location['address_parts']['type'];
			$map['address']['streetName'] = $location['address_parts']['street'];
			$map['address']['streetNumber'] = $location['address_parts']['number'];

			if (!empty($location['address_parts']['stair'])) {
				$map['address']['stair'] = $location['address_parts']['stair'];
			}

			if (!empty($location['address_parts']['floor'])) {
				$map['address']['floorNumber'] = $location['address_parts']['floor'];
			}

			if (!empty($location['address_parts']['door'])) {
				$map['address']['doorNumber'] = $location['address_parts']['door'];
			}

			$map['address']['city'] = $location['city'];
			$map['address']['province'] = $location['state'];
			$map['address']['country'] = $location['country'];
			$map['address']['hideStreetNumber'] = empty($location['show_address']) ? true : !$location['show_address'];
		}

		$map['features']['area'] = $item['size'];
		$map['features']['unit'] = $item['size_unit'] == 'sqm' ? '1' : '2';
		$map['features']['bedrooms'] = $item['bedrooms'];
		$map['features']['bathrooms'] = $item['baths'];
		$map['features']['balcony'] = !empty($item['features']['balcony']);
		$map['features']['elevator'] = !empty($item['features']['elevator']);
		$map['features']['furnished'] = !empty($item['features']['furnished']);
		$map['features']['equiped'] = false;

		if($item['ec_pending'] || empty($item['ec'])) {
			$map['energy_certification']['description'] = 'N';
		} else {
			$map['energy_certification']['description'] = $item['ec'];
		}
		
		$map['images'] = $this->getImages();
		//$map['plans'] = [];

		$map['status'] = $this->getStatus();
		$map['contactEmail'] = !empty($this->config['email']) ? $this->config['email'] : '';
		$map['contactPhone'] = !empty($this->config['phone']) ? $this->config['phone'] : '';

		return $map;
	}

	/**
	 * @return boolean
	 */
	public function valid() {
		if(!$this->validType()){
			$this->errors [] = \Lang::get('validation.type');
			return false;
		}
		
		if ($this->isTransfer()) {
			$this->errors [] = \Lang::get('validation.transfer');
			return false;
		}
				
		$data = array_merge($this->item, $this->config);
				
		$rules = [
			'id' => 'required',
			'type' => 'required',
			'price' => 'required',
			'location.lat' => 'required_without:location.address_parts.type,'
			. 'location.address_parts.street,location.address_parts.number,location.city,location.state,location.country',
			'location.lng' => 'required_without:location.address_parts.type,'
			. 'location.address_parts.street,location.address_parts.number,location.city,location.state,location.country',
			'location.address_parts.type' => 'required_without:location.lat,location.lng',
			'location.address_parts.street' => 'required_without:location.lat,location.lng',
			'location.address_parts.number' => 'required_without:location.lat,location.lng',
			'location.city' => 'required_without:location.lat,location.lng',
			'location.state' => 'required_without:location.lat,location.lng',
			'location.country' => 'required_without:location.lat,location.lng',
			'size' => 'required',
			'size_unit' => 'required',
			'bedrooms' => 'required',
			'baths' => 'required',
			'images.0' => 'required',
			'access_token' => 'required',
			'access_token_secret' => 'required',
		];

		$validator = \Validator::make($data, $rules, []);
		if ($validator->fails()) {
			$this->errors = $validator->errors()->all();
		}

		return empty($this->errors);
	}

	/**
	 * @return true
	 */
	protected function validType() {
		return !in_array($this->item['type'], ['ranche', 'state', 'hotel',
					'aparthotel', 'building', 'lot', 'store', 'industrial']);
	}

	/**
	 * Piso -> 1
	 * Casa -> 2
	 * Local -> 3
	 * Parking -> 4
	 * Solar/Parcela -> 5
	 * Oficina -> 6
	 * 
	 * @return string
	 */
	protected function getType() {
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
		}
	}

	/**
	 * Alquiler -> 1
	 * Venta -> 2
	 * 
	 * @return string
	 */
	protected function getMode() {
		if ($this->isRent()) {
			return '1';
		} elseif ($this->isSale()) {
			return '2';
		}
	}

	/**
	 * @return string
	 */
	protected function getCurrency() {
		return '€';
	}

	/**
	 * @return array
	 */
	protected function getImages() {
		$pictures = [];
		foreach ($this->item['images'] as $counter => $image) {
			$pictures[] = [
				'url' => $image,
				'principal' => $counter == 0
			];
		}
		return $pictures;
	}

	/**
	 * 1 Disponible
	 * 99 Delete
	 * @return string
	 */
	protected function getStatus() {
		return '1';
	}
	
}
