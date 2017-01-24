<?php

namespace App\Marketplaces\Yaencontre;

class AttributesHandler {

	const SEP = '@';
	
	protected $states;
	protected $cities_with_zones;

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
	 * @param integer $cityId
	 * @return array
	 */
	protected function getZones($cityId) {
		$zoneLocal = dirname(__FILE__) . "/assets/getZonesByCity-$cityId.xml";
		if (!file_exists($zoneLocal)) {
			$zoneRemote = file_get_contents("http://atlas.yaencontre.com/api/getZones?cityId=$cityId");
			file_put_contents($zoneLocal, $zoneRemote);
		}

		$response = simplexml_load_file($zoneLocal);
		$responseXML = $response->asXML();
		$responseArray = XML2Array::createArray($responseXML);

		$return = [];
		$zones = $responseArray['zones']['zone'];
		foreach ($zones as $zone) {
			if (!isset($zone['name'])) { //only 1 zone
				$zoneName = $zones['name']['@cdata'];
				$zoneId = $zones['@attributes']['id'];
			} else {//multiples zones
				$zoneName = $zone['name']['@cdata'];
				$zoneId = $zone['@attributes']['id'];
			}

			$return[$zoneId] = $zoneName;
		}
		return $return;
	}

	/**
	 * @param string $cityName
	 * @return boolean
	 */
	protected function isZoneRequired($cityName) {
		if (empty($this->cities_with_zones)) {
			$file = dirname(__FILE__) . '/assets/cities_with_zones.php';
			$this->cities_with_zones = include $file;
		}
		return in_array($cityName, $this->cities_with_zones);
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
		} else {
			$citiesFile = dirname(__FILE__) . '/assets/getCitiesByCountry-ES.xml';
			$response = simplexml_load_file($citiesFile);
			$responseXML = $response->asXML();
			$responseArray = XML2Array::createArray($responseXML);

			$cities = $responseArray['cities']['city'];
			foreach ($cities as $city) {
				$cityName = $city['name']['@cdata'];
				$cityId = $city['@attributes']['id'];
				$stateId = $city['@attributes']['stateId'];
				$stateName = $this->getProvinciaLabel($stateId);

				if ($this->isZoneRequired($cityName)) {
					$zones = $this->getZones($cityId);
					foreach ($zones as $zoneId => $zoneName) {
						$id = $cityName . self::SEP . $cityId . self::SEP .
								$stateName . self::SEP . $stateId . self::SEP . $zoneName . self::SEP . $zoneId;						
						$label = $cityName . " ($zoneName)";
						$attribute['values'][] = ['id' => $id, 'label' => $label];
					}
				} else { // cityh without zones
					$id = $cityName . self::SEP . $cityId . self::SEP . $stateName . self::SEP . $stateId;
					$label = $cityName;
					$attribute['values'][] = ['id' => $id, 'label' => $label];
				}
			}

			// Sort by name
			usort($attribute['values'], function($a, $b) {
				$unwanted_array = array('Š' => 'S', 'š' => 's', 'Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E',
					'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U',
					'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c',
					'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o',
					'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y');

				return strtr($a['label'], $unwanted_array) > strtr($b['label'], $unwanted_array);
			});

			// Save values file
			file_put_contents($valuesFile, '<?php return ' . var_export($attribute['values'], true) . ';');
		}

		return $attribute;
	}

}
