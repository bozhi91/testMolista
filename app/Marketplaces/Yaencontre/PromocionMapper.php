<?php

namespace App\Marketplaces\Yaencontre;

class PromocionMapper extends BaseMapper {

	/**
	 * @return array
	 */
	public function map() {
		$item = $this->item;

		$map = [];

		list($operationId, $operationName) = $this->getOperations();
		$map['operacion@id=' . $operationId] = $operationName;

		$map['url'] = '';

		//required name of promo
		$titles = $item['title'];
		$firstTitle = reset($titles);
		$map['nombre'] = $firstTitle;

		$map['ubicacion'] = $this->getUbicacion();
		$map['direcciones'] = $this->getDirecciones();

		if ($this->isRent()) {
			$map['precio_desde@periodicidad=' . $this->getPeriodicidad()] = $item['price'];
		} else {
			$map['precio_desde'] = $item['price'];
		}

		$map['disponibilidad'] = '';
		$map['sup_desde'] = $this->convertSize($item['size']);
		$map['num_hab_desde'] = $item['rooms'];
		$map['num_ban_desde'] = $item['baths'];
		$map['certificacion_energetica'] = $this->getCertificacionEnergetica();
		$map['certificacion_energetica_etiqueta'] = '';

		foreach ($item['title'] as $locale => $title) {
			$map['titulos']['titulo@idioma=' . $locale] = $title;
		}

		foreach ($item['description'] as $locale => $description) {
			$map['descripciones']['descripcion@idioma=' . $locale] = $description;
		}

		$map['info_hipotecas'] = '';
		$map['caracteristicas'] = '';
		$map['memoria_calidad'] = '';
		$map['adjuntos'] = $this->getImages();
		$map['tipologias'] = $this->getTipologias();

		return $map;
	}

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
			'rooms' => 'required',
			'baths' => 'required',
			'title.es' => 'required',
			'description.es' => 'required',
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
	protected function getDirecciones() {
		$item = $this->item;
		$l = $item['location'];

		$direccion = [];
		$direccion['direccion']['tipo_via@id='] = ''; //$ubicacion['tipo_via@id=0'] = 'avenida'; getTipoDeVia()
		$direccion['direccion']['nombre_via'] = !empty($l['address_parts']['street']) ? $l['address_parts']['street'] : '';
		$direccion['direccion']['num_via'] = !empty($l['address_parts']['number']) ? $l['address_parts']['number'] : '';

		return $direccion;
	}

	/**
	 * @return array
	 */
	protected function getTipologias() {
		//Treat tipologia as referencia
		$mapper = new ReferenciaMapper(
				$this->item
				, $this->iso_lang
				, $this->config);

		$tipologiaItem = $mapper->map();

		$property = $this->item;
		$timezone = new \DateTimeZone(\Config::get('app.timezone'));
		$datetime = new \DateTime($property['updated_at'], $timezone);
		$timestamp = $datetime->format("U");
		$key = 'tipologia@id=' . $property['id'] . '@timestamp=' . $timestamp;

		$returnItem = [$key => $tipologiaItem];
		return $returnItem;
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

		list($zoneId, $zoneLabel) = $this->getZoneData();

		if($zoneId && $zoneLabel) {
			$ubicacion['zona@id=' .$zoneId] = $zoneLabel;
		} else {
			$ubicacion['zona@id='] = ''; //tag required
		}

		//Opcionales
		$ubicacion['zona_libre'] = '';
		$ubicacion['cod_postal'] = !empty($l['zipcode']) ? $l['zipcode'] : '';
		$ubicacion['latitud'] = !empty($l['lat']) ? $l['lat'] : '';
		$ubicacion['longitud'] = !empty($l['lng']) ? $l['lng'] : '';

		return $ubicacion;
	}

}
