<?php

namespace App\Marketplaces\Properati;

class Mapper extends \App\Marketplaces\Mapper {

	/**
	 * Maps a Molista item to Properati format according to:
	 * http://www.properati.com.ar/feed
	 *
	 * @return array
	 */
	public function map() {
		$item = $this->item;

		$map = [];
		$map['id'] = $item['id'];
		$map['url'] = $this->translate($item['url']);
		$map['title'] = $this->translate($item['title']);
		$map['content'] = $this->translate($item['description']); //min 30

		$map['type'] = $this->getType();
		$map['property_type'] = $this->getPropertyType();

		$map['address'] = $item['location']['address'];

		$country_id = \App\Models\Geography\Country::where('code', $item['location']['country'])->value('id');
		$countryLabel = \App\Models\Geography\CountryTranslation::where('country_id', $country_id)
				->where('locale', $this->iso_lang)->value('name');

		$map['country'] = $countryLabel;
		$map['region'] = $item['location']['territory'];

		$map['agency']['id'] = $item['site_id'];
		$map['agency']['name'] = $this->config['name'];
		$map['agency']['phone'] = $this->config['phone'];
		$map['agency']['email'] = $this->config['email'];
		//$map['agency']['address'] = '';
		//$map['agency']['city_area'] = '';
		//$map['agency']['city'] = '';
		//$map['agency']['region'] = '';
		//$map['agency']['country'] = '';
		//$map['agency']['logo_url'] = '';

		$map['price@currency=' . $item['currency'] . '@period=monthly'] = ceil($item['price']);

		if(!empty($item['images'])){
			$map['pictures']['picture'] = $this->getImages();
		}

		$map['city'] = $item['location']['city'];
		$map['city_area'] = $item['location']['district'];
		$map['postcode'] = $item['location']['zipcode'];
		$map['floor_number'] = isset($item['location']['address_parts']) ?
				$item['location']['address_parts']['floor'] : '';
		$map['latitude'] = $item['location']['lat'];
		$map['longitude'] = $item['location']['lng'];
		//$map['orientation'] = '';
		//$map['floor_area']  = '';
		//$map['floor_area_open'] = '';
		$map['plot_area@unit=' . $this->getSizeUnit()] = $this->getSize();

		$map['rooms'] = $item['rooms'];
		//$map['bedrooms'] = '';
		$map['bathrooms'] = $item['baths'];
		//$map['condition'] = '';
		$map['year'] = $item['construction_year'];
		//$map['blueprint_url'] = '';

		$map['is_new'] = !$item['second_hand'];

		if (isset($item['features']['furnished'])) {
			$map['is_furnished'] = true;
		}

		if (isset($item['features']['parking'])) {
			$map['parking'] = true;
		}

		if (isset($item['features']['exterior'])) {
			$map['patio'] = true;
		}

		if (isset($item['features']['terrace'])) {
			$map['terrace'] = true;
		}

		if (isset($item['features']['balcony'])) {
			$map['balcony'] = true;
		}

		//$map['expiration_date'] = '';
		//$map['chat_url'] = '';
		//$map['group_id'] = '';

		if (isset($item['features'])) {
			$map['features']['feature'] = $this->getFeatures();
		}

		return $map;
	}

	/**
	 * @return boolean
	 */
	public function valid() {

		if ($this->isTransfer()) {
			$this->errors []= \Lang::get('validation.transfer');
            return false;
		}

		$data = array_merge($this->item, $this->config);

		$rules = [
			'id' => 'required',
			'url' => 'required',
			'title' => 'required',
			'type' => 'required',
			'description.' . $this->iso_lang => 'required|min:30',
			'location.address' => 'required',
			'location.country' => 'required',
			'site_id' => 'required',
			'name' => 'required',
			'phone' => 'required',
			'email' => 'required',
			'construction_year' => 'regex:#\d{4}#'
		];

		$validator = \Validator::make($data, $rules, []);
		if ($validator->fails()) {
			$this->errors = $validator->errors()->all();
		}

		return empty($this->errors);
	}

	/**
	 * @return array
	 */
	protected function getFeatures() {
		$features = [];
		foreach ($this->item['features'] as $key => $feature) {
			$features[] = $this->translate($feature);
		}
		return $features;
	}

	/**
	 * @return integer
	 */
	protected function getSize() {
		return round($this->item['size']);
	}

	/**
	 * @return string
	 */
	protected function getSizeUnit() {
		switch ($this->item['size_unit']) {
			case 'sqm': return 'metros';
			case 'sqf': return 'pies';
		}
	}

	/**
	 * 	for rent
	 * 	for sale
	 * 	country house rentals
	 * 	for rent local
	 * 	for sale local
	 * 	land for sale
	 * 	office for rent
	 * 	office for sale
	 * 	overseas
	 * 	parking for rent
	 * 	parking for sale
	 * 	roommate
	 * 	short term rentals
	 * 	transfer local
	 * 	unlisted foreclosure
	 * 	warehouse for rent
	 * 	warehouse for sale
	 *
	 *  @return string
	 */
	protected function getType() {
		return $this->isRent() ? 'for rent' : 'for sale';
	}

	/**
	 * 	apartment
	 * 	building
	 * 	country
	 * 	country house
	 * 	farm
	 * 	garage
	 * 	hangar
	 * 	hotel
	 * 	house
	 * 	lot
	 * 	office
	 * 	other
	 * 	ph
	 * 	share apartment
	 * 	store
	 * 	timeshare
	 *
	 * @return string
	 */
	protected function getPropertyType() {
		switch ($this->item['type']) {
			case 'store': return 'store';
			case 'lot': return 'lot';
			case 'duplex':
			case 'house':
			case 'penthouse':
			case 'villa':
				return 'house';
			case 'hotel':
				return 'hotel';
			case 'flat':
			case 'apartment':
			case 'aparthotel':
				return 'apartment';
			case 'building':
				return 'building';
			case 'state':
				return 'country';
			case 'farmhouse':
				return 'country house';

			default: return 'other';
		}
	}

	/**
	 * @return array
	 */
	protected function getImages() {
		$pictures = [];
		foreach ($this->item['images'] as $i => $image) {
			$pictures[] = [
				'picture_url' => $image,
					//'picture_title' => ''
			];
		}
		return $pictures;
	}

}
