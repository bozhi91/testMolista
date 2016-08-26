<?php

namespace App\Marketplaces\Yaencontre;

class AttributesHandler {

	/**
	 * Will get 3 dropdown attributes. Country, Province, Cities
	 * De momento solo Spain...
	 * 
	 * @return array
	 */
	public function getAttributes() {
		$attributes = [];
		$attributes[] = $this->getCityAttribute();
		return $attributes;
	}

	/**
	 * @param integer $provinceId
	 * @return string
	 */
	public function getProvinciaLabel($provinceId) {
		$stateFile = dirname(__FILE__) . '/assets/getStates-ES.xml';
		$response = simplexml_load_file($stateFile);
		$responseXML = $response->asXML();
		$responseArray = XML2Array::createArray($responseXML);

		$states = $responseArray['states']['state'];
		foreach ($states as $state) {
			$stateName = $state['name']['@cdata'];
			$stateId = $state['@attributes']['id'];
			if ($stateId == $provinceId) {
				return $stateName;
			}
		}
	}

	/**
	 * @return array
	 */
	protected function getCityAttribute() {
		$attribute = [
			'id' => 'yaencontre-city',
			'label' => 'City',
			'description' => '',
			'type' => 'dropdown',
			'required' => true,
			'values' => []
		];

		$countryFile = dirname(__FILE__) . '/assets/getCitiesByCountry-ES.xml';
		$response = simplexml_load_file($countryFile);
		$responseXML = $response->asXML();
		$responseArray = XML2Array::createArray($responseXML);

		$cities = $responseArray['cities']['city'];
		foreach ($cities as $city) {
			$cityName = $city['name']['@cdata'];
			$cityId = $city['@attributes']['id'];
			$stateId = $city['@attributes']['stateId'];
			$stateName = $this->getProvinciaLabel($stateId);

			$attribute['values'][] = [
				'id' => $cityName . '-' . $cityId . '-' . $stateName . '-' . $stateId,
				'label' => $cityName
			];
		}

		return $attribute;
	}

}
