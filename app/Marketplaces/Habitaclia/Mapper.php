<?php

namespace App\Marketplaces\Habitaclia;
use DB;

class Mapper extends \App\Marketplaces\Mapper {

	/**
	 * Maps a Molista item to habitaclia format
	 * @return array
	 */
	public function map() {

		$item = $this->item;
		$map = [];

		$map['id_inmueble'] = $item['id'];
		$map['referencia'] = $item['reference'];

		//Opcional Agency data
		$map['id_sucursal'] = ''; //site id?
		$map['propietario'] = '';
		$map['email_sucursal'] = '';
		$map['email_comercializadora'] = $this->config['email'];
		//Opcional

		$map['venta_01'] = $this->isSale() ? 1 : 0;
		$map['alquiler_01'] = $this->isRent() ? 1 : 0;
		$map['alquiler_opcion_venta_01'] = 0;
		$map['traspaso_01'] = $this->isTransfer() ? 1 : 0;
		$map['alquiler_temporada_01'] = 0;

		$map['precio_venta'] = $this->isSale() ? $item['price'] : 0;
		$map['precio_alquiler'] = $this->isRent() ? $item['price'] : 0;
		$map['precio_alquiler_opcion_compra'] = 0;
		$map['precio_traspaso'] = $this->isTransfer() ? $item['price'] : 0;
		$map['precio_alquiler_temporada'] = 0;

		list($tip_inm, $tipo, $tip_inm2, $subtipo) = $this->getTipos();
		$map['tipo'] = $tipo;
		$map['tip_inm'] = $tip_inm;
		$map['subtipo'] = $subtipo;
		$map['tip_inm2'] = $tip_inm2;

		list($provincia, $cod_prov) = $this->getProvincia();
		$map['provincia'] = $provincia;
		$map['cod_prov'] = $cod_prov;

		list($poblacion, $cod_pob) = $this->getPoblacion();
		$map['localidad'] = $poblacion;
		$map['cod_pob'] = $cod_pob;

		$map['ubicacion'] = $item['location']['address'];
		$map['cp'] = self::h($item['location']['zipcode']);

		list($zona, $cod_zona) = $this->getZone();
		$map['cod_zona'] = self::h($cod_zona);

		$map['banos'] = self::h($item['baths']);
		$map['aseos'] = self::h($item['toilettes']);
		$map['habitaciones'] = self::h($item['rooms']);

		$map['m2construidos'] = $item['size'] ? $this->convertSize($item['size']) : '';
		$map['m2utiles'] = $item['size_real'] ? $this->convertSize($item['size_real']) : '';
		$map['m2terraza'] = '';
		$map['m2jardin'] = '';
		$map['m2salon'] = '';

		$map['destacado'] = self::h($item['title'][$this->iso_lang]);
		$map['descripcion'] = self::h($item['description'][$this->iso_lang]);
		$map['calidades'] = '';
		$map['urbanizacion'] = '';
		$map['estadococina'] = '';
		$map['numeroplanta'] = '';
		$map['anoconstruccion'] = self::h($item['construction_year']);
		$map['gastoscomunidad'] = '';

		$map['garaje_01'] = self::b($item['features']['garage']);
		$map['terraza_01'] = self::b($item['features']['terrace']);
		$map['ascensor_01'] = self::b($item['features']['elevator']);
		$map['trastero_01'] = '';
		$map['piscina_01'] = self::b($item['features']['pool']);
		$map['buhardilla_01'] = '';
		$map['lavadero_01'] = '';
		$map['jardin_01'] = self::b($item['features']['garden']);
		$map['piscinacom_01'] = '';
		$map['eqdeportivos_01'] = '';
		$map['vistasalmar_01'] = '';
		$map['vistasalamontana_01'] = '';
		$map['vistasalaciudad_01'] = '';
		$map['cercatransportepub_01'] = '';
		$map['aireacondicionado_01'] = self::b($item['features']['air-conditioning']);
		$map['calefaccion_01'] = self::b($item['features']['heating']);
		$map['chimenea_01'] = '';
		$map['cocina_office'] = '';
		$map['despacho'] = '';
		$map['amueblado'] = self::b($item['features']['furnished']);
		$map['vigilancia'] = '';
		$map['escaparate'] = '';

		$map['m2_almacen'] = '';
		$map['m2_fachada'] = '';
		$map['centro_neg'] = '';
		$map['planta_diaf'] = '';
		$map['m2_terreno'] = $item['type'] == 'lot' ? $this->convertSize($item['size']) : '';
		$map['m2_industrial'] = $item['type'] == 'industrial' ? $this->convertSize($item['size']) : '';
		$map['m2_oficinas'] = '';
		$map['m_altura'] = '';
		$map['entrada_camion'] = '';
		$map['vestuarios'] = '';
		$map['edificable'] = '';
		$map['calif_energetica'] = self::h($item['ec']);
		$map['de_banco'] = '';

		$map['photos']['photo'] = $this->getImages();
		$map['videos'] = '';

        $map['videos_360']['video'] = $this->get_3d_url();

		$map['mapa']['latitud'] = self::h($item['location']['lat']);
		$map['mapa']['longitud'] = self::h($item['location']['lng']);
		$map['mapa']['zoom'] = 16; //14 15 16 17
		$map['mapa']['puntero'] = !empty($item['show_address']) && $item['show_address'] ? 1 : 0;

		$map['producto_premium'] = 0;
		$map['producto_destacado'] = 0;
		$map['producto_oportunidad'] = 0;

		return $map;
	}

	private function get_3d_url(){
	    $videos = array();
        $item = $this->item;

        $videos3d = DB::table('properties')
            ->select('url_3d')
            ->where('id',$item['id'])
            ->get();

        foreach ($videos3d as $video){
            $token = strtok($video->url_3d, ".");
            $token = strtok(".");
            array_push($videos,array("plataforma"=>$token,"url"=>$video->url_3d));
        }

	    return $videos;
    }

	/**
	 * @return boolean
	 */
	public function valid() {


		if (in_array($this->item['type'], ['garage', 'plot'])){
            $this->errors []= \Lang::get('validation.type');
            return false;
        }

		$data = array_merge($this->item, $this->config);

		$rules = [
			'id' => 'required',
			'reference' => 'required',
			'type' => 'required',
			'attributes.habitaclia-city' => 'required',
			'location.address' => 'required',
			'email' => 'required',
		];

		$validator = \Validator::make($data, $rules, []);
		if ($validator->fails()) {
			$this->errors = $validator->errors()->all();
		}

		echo json_encode($validator);
		die;
		/*return empty($this->errors);*/
	    return true;
	}

	/**
	 * Small helper
	 * @param string|null $value
	 * @return string
	 */
	private static function h(&$value) {
		return !empty($value) ? $value : '';
	}

	/**
	 * @param mixed $value
	 * @return integer
	 */
	private static function b(&$value) {
		return isset($value) ? 1 : 0;
	}

	/**
	 * @return array
	 */
	protected function getTipos() {
		switch ($this->item['type']) {
			case 'flat': return [1, 'Vivienda', 1, 'Piso'];
			case 'duplex': return [1, 'Vivienda', 2, 'Duplex'];
			case 'villa':
			case 'penthouse':
			case 'house':
			case 'chalet':
			case 'terraced_house':
				return [1, 'Vivienda', 3, 'Casa'];
			case 'apartment': return [1, 'Vivienda', 4, 'Apartamento'];
			case 'farmhouse': return [1, 'Vivienda', 8, 'Masía'];
			case 'bungalow': return [9, 'Inmuebles singulares', 8, 'Módulo cámping'];
			case 'ranche':
			case 'state':
				return [5, 'Terrenos y Solares', 2, 'Finca rústica'];
			case 'hotel':
			case 'aparthotel':
				return [10, 'Negocio', 4, 'Hotel'];
			case 'office':
				return [2, 'Oficina', 1, 'Oficina'];
			case 'building': return [8, 'Inversiones', 3, 'Edificios'];
			case 'lot': return [5, 'Terrenos y Solares', 1, 'Terreno residencial'];
			case 'store': return [3, 'Local', 1, 'Local Comercial'];
			case 'industrial': return [4, 'Industrial', 1, 'Nave Industrial'];
			default: return [1, 'Vivienda', 1, 'Piso'];
		}
	}

	/**
	 * @return array
	 */
	protected function getProvincia() {
		$valor = $this->item['attributes']['habitaclia-city'];
		$explodedValor = explode(AttributesHandler::SEP, $valor);
		return [$explodedValor[0], $explodedValor[0]];
	}

	/**
	 * @return array
	 */
	protected function getPoblacion() {
		$valor = $this->item['attributes']['habitaclia-city'];
		$explodedValor = explode(AttributesHandler::SEP, $valor);

		return [$explodedValor[0], $explodedValor[0]];
	}

	/**
	 * @return array
	 */
	protected function getZone() {
		$valor = $this->item['attributes']['habitaclia-city'];
		$explodedValor = explode(AttributesHandler::SEP, $valor);
		if (isset($explodedValor[4]) && isset($explodedValor[5])) {
			return [$explodedValor[5], $explodedValor[4]];
		}
		return [null, null];
	}

	/**
	 * @param float $size
	 * @return int
	 */
	protected function convertSize($size) {
		switch ($this->item['size_unit']) {
			case 'sqm': return round($size, 2);
			case 'sqf': return round(($size * 0.092903), 2);
		}
	}

	/**
	 * @return array
	 */
	protected function getImages() {
		$pictures = [];
		foreach ($this->item['images'] as $counter => $image) {
			$pictures[] = [
				'url' => $image,
				'numimagen' => $counter,
			];
		}
		return $pictures;
	}

}
