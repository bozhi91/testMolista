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

			// Sort by name
			usort($attribute['values'], function($a, $b) {
				$unwanted_array = array(    'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
                            'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
                            'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
                            'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
                            'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
				
			    return strtr($a['label'], $unwanted_array) > strtr($b['label'], $unwanted_array);
			});

			// Save values file
			file_put_contents($valuesFile, '<?php return '. var_export($attribute['values'], true).';');
		}

		return $attribute;
	}

}
