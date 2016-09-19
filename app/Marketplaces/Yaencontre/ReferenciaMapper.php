<?php

namespace App\Marketplaces\Yaencontre;

class ReferenciaMapper extends BaseMapper {

	/**
	 * @return array
	 */
	public function map() {
		$item = $this->item;

		$map = [];

		list($operationId, $operationName) = $this->getOperations();
		$map['operacion@id=' . $operationId] = $operationName;

		list($tipoId, $tipoName) = $this->getPropertyTypes();
		$map['tipo@id=' . $tipoId] = $tipoName;

		$map['ubicacion'] = $this->getUbicacion();

		if ($this->isRent()) {
			$map['precio@periodicidad=' . $this->getPeriodicidad()] = $item['price'];
		} else {
			$map['precio'] = $item['price'];
		}

		$map['disponibilidad'] = '';
		$map['num_reg_alquiler_turistico'] = '';

		$map['m2_construidos'] = $this->convertSize($item['size']);
		$map['m2_utiles'] = $this->convertSize($item['size_real']);

		$map['habitaciones'] = $item['rooms'];
		$map['banyos'] = $item['baths'];
		$map['nuevo'] = $item['newly_build'] ? 1 : 0;
		$map['certificacion_energetica'] = $this->getCertificacionEnergetica();
		$map['certificacion_energetica_etiqueta'] = '';

		foreach ($item['title'] as $locale => $title) {
			$map['titulos']['titulo@idioma=' . $locale] = $title;
		}

		foreach ($item['description'] as $locale => $description) {
			$map['descripciones']['descripcion@idioma=' . $locale] = $description;
		}

		$map['caracteristicas'] = $this->getCaracteristicas();
		$map['adjuntos'] = $this->getImages();

		return $map;
	}

	/**
	 * @return boolean
	 */
	public function valid() {
		$data = array_merge($this->item, $this->config);

		if ($this->isTransfer()) {
			$this->errors [] = \Lang::get('validation.transfer');
			return false;
		}

		$rules = [
			'id' => 'required',
			'type' => 'required',
			'attributes.yaencontre-city' => 'required',
			'price' => 'required',
			'size' => 'required',
			'size_real' => 'required',
			'description.es' => 'required',
			'oficina' => 'required'
		];

		$validator = \Validator::make($data, $rules, []);
		if ($validator->fails()) {
			$this->errors = $validator->errors()->all();
		}

		return empty($this->errors);
	}

	/**
	 * 7 apartamento
	 * 8 ático
	 * 9 dúplex
	 * 10 estudio
	 * 11 piso
	 * 12 ático-dúplex
	 * 13 bajo
	 * 14 loft
	 * 16 casa
	 * 17 masía
	 * 18 casa adosada
	 * 19 chalet
	 * 20 cortijo
	 * 22 casa pareada
	 * 23 bungalow
	 * 24 villa
	 * 26 finca rústica
	 * 28 local
	 * 29 oficina
	 * 30 bar
	 * 31 restaurante
	 * 32 local comercial
	 * 34 residencia
	 * 36 garaje
	 * 37 trastero
	 * 38 terreno
	 * 39 solar
	 * 40 parcela
	 * 41 parcela industrial
	 * 42 terreno industrial
	 * 43 nave
	 * 44 almacén
	 * 45 nave industrial
	 * 46 edificio
	 * 47 hotel
	 * 48 aparta-hotel
	 * 49 cueva
	 * 50 casa rural
	 *
	 * @return array
	 */
	protected function getPropertyTypes() {
		switch ($this->item['type']) {
			case 'apartment': return [7, 'apartamento'];
			case 'duplex': return [9, 'duplex'];
			case 'house': return [16, 'casa'];
			case 'penthouse': return [8, 'ático'];
			case 'villa': return [24, 'villa'];
			case 'aparthotel': return [48, 'aparta-hotel'];
			case 'hotel': return [47, 'hotel'];
			case 'flat': return [11, 'piso'];
			case 'lot': return [38, 'terreno'];
			case 'store': return [28, 'local'];
			case 'ranche': return [26, 'finca rústica'];
			case 'chalet': return [19, 'chalet'];
			case 'bungalow': return [23, 'bungalow'];
			case 'building': return [46, 'edificio'];
			case 'industrial': return [45, 'nave industrial'];
			case 'state': return [26, 'finca rústica'];
			case 'farmhouse': return [17, 'masía'];
		}
	}

	/**
	 * @return array
	 */
	protected function getCaracteristicas() {
		$item = $this->item;

		$caracteristicas = [];

		if (!empty($item['features']['air-conditioning'])) {
			$caracteristicas['extra@id=aire_acondicionado'] = 2;
		}

		if (!empty($item['features']['alarm'])) {
			$caracteristicas['extra@id=alarma'] = 1;
		}

		if (!empty($item['features']['furnished'])) {
			$caracteristicas['extra@id=amueblado'] = 1;
		}

		if (!empty($item['construction_year'])) {
			$caracteristicas['extra@id=anyo_construccion'] = $item['construction_year'];
		}

		if (!empty($item['features']['built-in-closets'])) {
			$caracteristicas['extra@id=armarios_empotrados'] = 1;
		}

		if (!empty($item['features']['elevator'])) {
			$caracteristicas['extra@id=ascensor'] = 1;
		}

		if (!empty($item['baths'])) {//toilets or bathrooms?
			//$caracteristicas['extra@id=aseos_num'] = $item['baths'];
		}

		if (!empty($item['features']['balcony'])) {
			$caracteristicas['extra@id=balcon'] = 1;
		}

		if (!empty($item['features']['heating'])) {
			$caracteristicas['extra@id=calefaccion'] = 1;
		}

		if (!empty($item['features']['garage'])) {
			$caracteristicas['extra@id=garaje'] = 1;
		}

		if (!empty($item['features']['garden'])) {
			$caracteristicas['extra@id=jardin'] = 1;
		}

		if (!empty($item['features']['pool'])) {
			$caracteristicas['extra@id=piscina'] = 1;
		}

		if (!empty($item['features']['terrace'])) {
			$caracteristicas['extra@id=terraza'] = 1;
		}

		if (!empty($item['features']['parking'])) {
			//parking_plazas 0 = “1”, 1 = “2”, 2 = “3+”
			//$caracteristicas['extra@id=parking_plazas'] = 0;
		}

		return $caracteristicas;
	}

	/**
	 * @return array
	 */
	protected function getUbicacion() {
		$item = $this->item;
		$l = $item['location'];

		$ubicacion = [];

		//Required
		$ubicacion['pais'] = 'ES';

		list($provinciaId, $provinciaLabel) = $this->getProvinciaData();
		$ubicacion['provincia@id=' . $provinciaId] = $provinciaLabel;

		list($poblacionId, $poblacionLabel) = $this->getPoblacionData();
		$ubicacion['poblacion@id=' . $poblacionId] = $poblacionLabel;

		//Opcionales
		$ubicacion['zona@id='] = '';
		$ubicacion['zona_libre'] = '';
		$ubicacion['cod_postal'] = !empty($l['zipcode']) ? $l['zipcode'] : '';
		$ubicacion['tipo_via@id='] = '';//$ubicacion['tipo_via@id=0'] = 'avenida'; getTipoDeVia()		
		$ubicacion['direccion'] = !empty($l['address_parts']['street']) ? $l['address_parts']['street'] : '';
		$ubicacion['direccion_num'] = !empty($l['address_parts']['number']) ? $l['address_parts']['number'] : '';
		$ubicacion['direccion_otra_info'] = '';
		$ubicacion['direccion_privada'] = !empty($l['show_address']) ? (int) $l['show_address'] : 0;
		$ubicacion['latitud'] = !empty($l['lat']) ? $l['lat'] : '';
		$ubicacion['longitud'] = !empty($l['lng']) ? $l['lng']  : '';

		return $ubicacion;
	}

}
