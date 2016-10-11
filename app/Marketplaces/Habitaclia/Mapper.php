<?php

namespace App\Marketplaces\Habitaclia;

class Mapper extends \App\Marketplaces\Mapper {

	/**
	 * Maps a Molista item to habitaclia format
	 * @return array
	 */
	public function map() {
		$item = $this->item;

		$map = [];
	
		$map['id_inmueble'] = '';
		$map['referencia'] = ''; //unique
		$map['id_sucursal'] = '';
		$map['propietario'] = '';
		$map['email_sucursal'] = '';
		$map['email_comercializadora'] = '';
		$map['venta_01'] = '';
		$map['alquiler_01'] = '';
		$map['alquiler_opcion_venta_01'] = '';
		$map['traspaso_01'] = '';
		$map['alquiler_temporada_01'] = '';
		$map['precio_venta'] = '';
		$map['precio_alquiler'] = '';
		$map['precio_alquiler_opcion_compra'] = '';
		$map['precio_traspaso'] = '';
		$map['precio_alquiler_temporada'] = '';
		$map['tipo'] = '';
		$map['tip_inm'] = '';
		$map['subtipo'] = '';
		$map['tip_inm2'] = '';
		$map['provincia'] = '';
		$map['cod_prov'] = '';
		$map['localidad'] = '';
		$map['cod_pob'] = '';
		$map['ubicacion'] = '';
		$map['cp'] = '';
		$map['cod_zona'] = '';
		$map['banos'] = '';
		$map['aseos'] = '';
		$map['habitaciones'] = '';
		$map['m2construidos'] = '';
		$map['m2utiles'] = '';
		$map['m2terraza'] = '';
		$map['m2jardin'] = '';
		$map['m2salon'] = '';
		$map['destacado'] = '';
		$map['descripcion'] = '';
		$map['calidades'] = '';
		$map['urbanizacion'] = '';
		$map['estadococina'] = '';
		$map['numeroplanta'] = '';
		$map['anoconstruccion'] = '';
		$map['gastoscomunidad'] = '';
		$map['garaje_01'] = '';
		$map['terraza_01'] = '';
		$map['ascensor_01'] = '';
		$map['trastero_01'] = '';
		$map['piscina_01'] = '';
		$map['buhardilla_01'] = '';
		$map['lavadero_01'] = '';
		$map['jardin_01'] = '';
		$map['piscinacom_01'] = '';
		$map['eqdeportivos_01'] = '';
		$map['vistasalmar_01'] = '';
		$map['vistasalamontana_01'] = '';
		$map['vistasalaciudad_01'] = '';
		$map['cercatransportepub_01'] = '';
		$map['aireacondicionado_01'] = '';
		$map['calefaccion_01'] = '';
		$map['chimenea_01'] = '';
		$map['cocina_office'] = '';
		$map['despacho'] = '';
		$map['amueblado'] = '';
		$map['vigilancia'] = '';
		$map['escaparate'] = '';
		$map['m2_almacen'] = '';
		$map['m2_fachada'] = '';
		$map['centro_neg'] = '';
		$map['planta_diaf'] = '';
		$map['m2_terreno'] = '';
		$map['m2_industrial'] = '';
		$map['m2_oficinas'] = '';
		$map['m_altura'] = '';
		$map['entrada_camion'] = '';
		$map['vestuarios'] = '';
		$map['edificable'] = '';
		$map['calif_energetica'] = '';
		$map['de_banco'] = '';
		
		//Client Reserva. Cobramos reserva.
		//Pro accepta / rechaza
		
		
		
		//rechaza. Buscamos a otro pro.
		
		
		
		
		
		$map['photos'] = '';
		/*<photo>
<url>http://www.ejemploinmobiliaria.com/foto1.pg</url>
<numimagen>1</numimagen>
<descimagen>
<![CDATA[ Fachada ]]>
</descimagen>
</photo>*/
		
		
		
		$map['videos'] = '';
		/*<video>
<url>http://www.youtube.com/watch?v=JRlYJKNcWx0</url>
</video>*/
		
		$map['mapa']['latitud'] = '';
		$map['mapa']['longitud'] = '';
		$map['mapa']['zoom'] = '';
		$map['mapa']['puntero'] = '';
		
		$map['producto_premium'] = '';
		$map['producto_destacado'] = '';
		$map['producto_oportunidad'] = '';

				
		
		
		
		
		return $map;
	}

	/**
	 * @return boolean
	 */
	public function valid() {
		return true;
	}
	
}