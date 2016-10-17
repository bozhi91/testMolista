<?php

namespace App\Marketplaces\Habitaclia;

class AttributesHandler {

	const SEP = '@';

	/**
	 * @return array
	 */
	public function getAttributes() {
		$attributes = [];
		$attributes[] = $this->getCityAttribute();
		return $attributes;
	}

	/**
	 * @return array
	 */
	protected function getCityAttribute() {
		$attribute = [
			'id' => 'habitaclia-city',
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
			$attribute['values'] = $this->loadData();


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

	/**
	 * @return array
	 */
	protected function loadData() {
		$provinciasFile = dirname(__FILE__) . '/assets/provincias.csv';
		$poblacionesFile = dirname(__FILE__) . '/assets/poblaciones.csv';
		$zonasFile = dirname(__FILE__) . '/assets/zonas.csv';

		$data = [];
		//Load provincias
		foreach (file($provinciasFile) as $line) {
			$exploded = explode(';', trim($line));
			$data[$exploded[0]] = [
				'label' => $exploded[1],
				'poblaciones' => [],
			];
		}

		//Load poblaciones
		foreach (file($poblacionesFile) as $line) {
			$exploded = explode(';', trim($line));
			$data[$exploded[0]]['poblaciones'][$exploded[1]] = [
				'label' => $exploded[2],
				'zonas' => []
			];
		}

		//Load zonas
		foreach (file($zonasFile) as $line) {
			$exploded = explode(';', trim($line));
			$data[$exploded[0]]['poblaciones']
					[$exploded[1]]['zonas'][$exploded[2]] = $exploded[3];
		}

		
		//Create attribute values
		$values = [];
		foreach($data as $provinceId => $province) {
			foreach($province['poblaciones'] as $poblacionId => $poblacion){
				
				$id = $provinceId . self::SEP .
							$province['label'] . self::SEP .
							$poblacionId . self::SEP . $poblacion['label'];
				
				
				if(!empty($poblacion['zonas'])){
					foreach($poblacion['zonas'] as $zonaId => $zonaLabel){
						$newId = $id . self::SEP . $zonaId . self::SEP . $zonaLabel;
						$label = $poblacion['label'] . " ($zonaLabel)";
						$values[] = ['id' => $newId, 'label' => $label];
					}
				} else {
					$label = $poblacion['label'];
					$values[] = ['id' => $id, 'label' => $label];
				}
			}
		}
		
		return $values;
	}

}
