<?php

namespace App\Marketplaces\Inmofactory;

abstract class Mapper extends \App\Marketplaces\Mapper {

	/**
	 * @return array
	 */
	public function map() {
		$item = $this->item;

		$map = [];

		$map['ExternalId'] = (string)$item['id'];
		//$map['ParentId'] = '';//If the property belongs to a promotion, its unique identifier
		$map['AgencyReference'] = $item['reference'];

		list($typeId, $subtypeId) = $this->getTypeId();
		$map['TypeId'] = $typeId;
		if ($subtypeId) {
			$map['SubTypeId'] = $subtypeId;
		}

		$map['IsNewConstruction'] = (bool)$item['newly_build'];
		$map['PropertyStatusId'] = $this->getStatus();
		//$map['ExpirationCauseId'] = '';
		$map['ShowSurface'] = true;
		$map['ContactTypeId'] = 1; //agencia
		//$map['ContactName'] = ''; //Complete name (and surname) of the property's contact person
		$map['IsPromotion'] = false;
		//$map['PromotionTypeId'] = '';
		//$map['BankAwardedId'] = '';
		//$map['ExpirationDate'] = '';
		$map['PropertyAddress'] = $this->getPropertyAddress();
		$map['PropertyDocument'] = $this->getPropertyDocuments();
		$map['PropertyFeature'] = $this->getPropertyFeatures();
		$map['PropertyContactInfo'] = $this->getPropertyContactInfo();
		$map['PropertyUser'] = $this->getPropertyUser();
		$map['PropertyTransaction'] = $this->getPropertyTransaction();
		$map['PropertyPublications'] = $this->getPropertyPublications();

		return $map;
	}

	/**
	 * @return boolean
	 */
	public function valid() {
		return true;
	}

	/**
	 * Identifies the type of the property within the following enumeration
	 * 
	 * 1	Piso
	 * 2	Casa
	 * 3	Local
	 * 4	Oficina
	 * 5	Edificio
	 * 6	Suelo
	 * 7	Industrial
	 * 8	Parking
	 * 10	Hotel
	 * 12	Trastero
	 * 
	 * @return array
	 */
	protected function getTypeId() {
		switch ($this->item['type']) {
			case 'apartment': return [1, 10];
			case 'duplex': return [1, 3];
			case 'house': return [2, 13];
			case 'lot': return [6, 53];
			case 'penthouse': return [1, 5];
			case 'store': return [12, null];
			case 'villa': return [2, 13];
			case 'ranch': return [2, 24];
			case 'flat': return [1, 9];
			case 'hotel': return [10, 75];
			case 'aparthotel': return [10, 73];
			case 'chalet': return [2, 20];
			case 'bungalow': return [2, 27];
			case 'building': return [5, null];
			case 'industrial': return [7, 64];
			case 'state': return [2, 24];
			case 'farmhouse': return [2, 21];
		}
	}

	/**
	 * 1    Disponible
	 * 2	Reservado
	 * 3	Captación
	 * 4	No disponible
	 * 5	En construcción
	 */
	protected function getStatus() {
		return 1;
	}

	/**
	 * 0	Indefinido
	 * 20	Andorra
	 * 250	Francia
	 * 620	Portugal
	 * 724	España
	 */
	protected function getCountryId() {
		switch ($this->item['location']['country']) {
			case 'AN': return 20;
			case 'FR': return 250;
			case 'PT': return 620;
			case 'ES': return 724;
			default: return 724;
		}
	}

	/**
	 * 0	Indefinido
	 * 1	Mostrar Calle y Número
	 * 2	Mostrar Calle
	 * 3	Mostrar Dirección Pública
	 */
	protected function getAddressVisibilityId() {
		return $this->item['location']['show_address'] ? 1 : 0;
	}

	/**
	 * @return array
	 */
	protected function getPropertyAddress() {
		$item = $this->item;

		$address = [];
		$address['ZipCode'] = $item['location']['zipcode'];
		$address['CountryId'] = $this->getCountryId();
		$address['Zone'] = $item['location']['city'];
		//$address['StreetTypeId'] = '';

		if (!empty($item['location']['address_parts'])) {
			$parts = $item['location']['address_parts'];
			$address['Street'] = $parts['street'];
			$address['Number'] = $parts['number'];
			//$address['FloorId'] = '';
			$address['Door'] = $parts['door'];
			$address['Stair'] = $parts['stair'];
		}

		$address['x'] = $item['location']['lng'];
		$address['y'] = $item['location']['lat'];
		$address['VisibilityModeId'] = $this->getAddressVisibilityId();
		return [$address];
	}

	/**
	 * @return array
	 */
	protected function getPropertyDocuments() {
		$documents = [];

		foreach ($this->item['images'] as $i => $image) {
			$document = [];
			$document['TypeId'] = 1;
			//$document['Description'] = '';
			$document['Url'] = $image;
			//$document['RoomTypeId'] = '';
			$document['FileTypeId'] = 0;
			$document['Visible'] = true;
			$document['SortingId'] = ++$i;
			$documents[] = $document;
		}
		return $documents;
	}

	/**
	 * @return array
	 */
	abstract protected function getPropertyPublications();

	/**
	 * @return array
	 */
	protected function getPropertyContactInfo() {
		$contacts = [];

		$contacts[] = [
			"TypeId" => 1,
			"Value" => $this->config['email'],
			"ValueTypeId" => 1
		];

		return $contacts;
	}

	/**
	 * @return array
	 */
	protected function getPropertyUser() {
		$users = [];

		$users[] = [
			"UserId" => (int)$this->config['agent_id'],
			"IsPrincipal" => true
		];

		return $users;
	}

	/**
	 * @return array
	 */
	protected function getPropertyTransaction() {
		$item = $this->item;
		$transaction = [];

		$transactions[] = [
			"TransactionTypeId" => $this->getTransactionTypeId(),
			//"CustomerPrice" => 0,
			"Price" => $item['price'],
			"PriceM2" => ($item['price'] / $this->getSize()),
			"CurrencyId" => 1,
			//"PaymentPeriodicityId" => 6,
			"ShowPrice" => true
		];

		return $transactions;
	}

	/**
	 * 1	Venta
	 * 3	Alquiler
	 * 4	Traspaso
	 * 7	A compartir
	 * 8	Alquiler vacacional
	 * 9	Alquiler con opcion a compra
	 */
	protected function getTransactionTypeId() {
		if ($this->isRent()) {
			return 3;
		} elseif ($this->isSale()) {
			return 1;
		} elseif ($this->isTransfer()) {
			return 4;
		}
	}

	/**
	 * @return array
	 */
	protected function getPropertyFeatures() {
		$item = $this->item;
		$features = [];
		list($typeId, $subtypeId) = $this->getTypeId();


		$features[] = $this->genFeature(1, [
			'DecimalValue' => floatval($item['size'])
		]);

		$features[] = $this->genFeature(3, [
			'TextValue' => $item['description'][$this->iso_lang]
		]);

		if (in_array($typeId, [1, 2, 10])) {
			$features[] = $this->genFeature(11, [
				'DecimalValue' => (int)$item['rooms']
			]);
		}

		if (in_array($typeId, [1, 2])) {
			$features[] = $this->genFeature(12, [
				'DecimalValue' => (int)$item['baths']
			]);
		}

		if (in_array($typeId, [1, 2, 3, 4])) {
			$features[] = $this->genFeature(13, [
				'DecimalValue' => (int)$item['toilettes']
			]);
		}

		if (in_array($typeId, [1, 3, 4, 5, 10])) {
			$features[] = $this->genFeature(22, [
				'BoolValue' => !empty($item['features']['elevator'])
			]);
		}

		if (in_array($typeId, [1, 2, 4, 5, 10])) {
			$features[] = $this->genFeature(23, [
				'BoolValue' => !empty($item['features']['parking'])
			]);
		}

		if (in_array($typeId, [1, 2, 5, 10])) {
			$features[] = $this->genFeature(25, [
				'BoolValue' => !empty($item['features']['pool'])
			]);
		}

		if (in_array($typeId, [2, 10])) {
			$features[] = $this->genFeature(26, [
				'BoolValue' => !empty($item['features']['garden'])
			]);
		}

		if (in_array($typeId, [1, 2, 4])) {
			$features[] = $this->genFeature(27, [
				'BoolValue' => !empty($item['features']['terrace'])
			]);
		}

		if (in_array($typeId, [1, 2, 4, 10])) {
			$features[] = $this->genFeature(29, [
				'BoolValue' => !empty($item['features']['heating'])
			]);
		}

		if (in_array($typeId, [1, 2, 3, 4, 10])) {
			$features[] = $this->genFeature(30, [
				'BoolValue' => !empty($item['features']['furnished'])
			]);
		}

		if (in_array($typeId, [1, 2, 3, 4, 5, 6, 7, 8, 10, 12])) {
			$features[] = $this->genFeature(57, [
				'DecimalValue' => floatval($item['size_real'])
			]);
		}

		if (in_array($typeId, [1, 2, 3, 4, 5, 6, 7, 8, 10, 12])) {
			$features[] = $this->genFeature(231, [
				'DecimalValue' => (int)$item['construction_year']
			]);
		}

		if (in_array($typeId, [1, 2, 8])) {
			$features[] = $this->genFeature(235, [
				'BoolValue' => !empty($item['features']['alarm'])
			]);
		}

		if (in_array($typeId, [1, 2, 3, 4, 10])) {
			$features[] = $this->genFeature(254, [
				'BoolValue' => !empty($item['features']['air-conditioning'])
			]);
		}

		if (in_array($typeId, [1, 2])) {
			$features[] = $this->genFeature(258, [
				'BoolValue' => !empty($item['features']['closet'])
			]);
		}

		if (in_array($typeId, [1, 2])) {
			$features[] = $this->genFeature(297, [
				'BoolValue' => !empty($item['features']['balcony'])
			]);
		}

		if (in_array($typeId, [1, 2, 3, 4, 5, 7, 10])) {
			$features[] = $this->genFeature(317, [
				'List' => $item['ec_pending'] ? 8 : $item['ec']
			]);
		}

		return $features;
	}

	/**
	 * @param integer $featureId
	 * @param array $mergeArr
	 * @return array
	 */
	protected function genFeature($featureId, $mergeArr) {
		return array_merge([
			"FeatureId" => $featureId,
			"LanguageId" => 4, //Español
				], $mergeArr);
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

}
