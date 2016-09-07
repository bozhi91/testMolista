<?php

namespace App\Marketplaces\Yaencontre;

class AttributesHandler {

	protected $states;
	protected $cities;

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
		if (empty($this->states)) {
			$stateFile = dirname(__FILE__) . '/assets/getStates-ES.xml';
			$response = simplexml_load_file($stateFile);
			$responseXML = $response->asXML();
			$responseArray = XML2Array::createArray($responseXML);

			$this->states = $responseArray['states']['state'];
		}

		foreach ($this->states as $state) {
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

		$valuesFile = dirname(__FILE__) . '/assets/cities.php';
		if (file_exists($valuesFile)) {
			$attribute['values'] = include $valuesFile;
		}
		else {

			if (empty($this->cities)) {
				$countryFile = dirname(__FILE__) . '/assets/getCitiesByCountry-ES.xml';
				$response = simplexml_load_file($countryFile);
				$responseXML = $response->asXML();
				$responseArray = XML2Array::createArray($responseXML);

				$this->cities = $responseArray['cities']['city'];
			}

			foreach ($this->cities as $city) {
				$cityName = $city['name']['@cdata'];
				$cityId = $city['@attributes']['id'];
				$stateId = $city['@attributes']['stateId'];
				$stateName = $this->getProvinciaLabel($stateId);

				$attribute['values'][] = [
					'id' => $cityName . '-' . $cityId . '-' . $stateName . '-' . $stateId,
					'label' => $cityName
				];
			}

			// Save values file
			file_put_contents($valuesFile, '<?php return '. var_export($attribute['values'], true).';');
		}

		return $attribute;
	}

}
