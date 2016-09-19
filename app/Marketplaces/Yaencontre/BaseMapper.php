<?php

namespace App\Marketplaces\Yaencontre;

abstract class BaseMapper extends \App\Marketplaces\Mapper {

	/**
	 * 1 compra
	 * 2 alquiler
	 * 3 alquiler de temporada
	 * 4 traspaso
	 * 7 alquiler opción compra
	 *
	 * @return array
	 */
	protected function getOperations() {
		if ($this->isSale()) {
			return [1, 'compra'];
		} elseif ($this->isRent()) {
			return [2, 'alquiler'];
		} elseif ($this->isTransfer()) {
			return [4, 'traspaso'];
		}
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
	 * 0 avenida
	 * 1 barrio
	 * 2 calle
	 * 3 colonia
	 * 4 carretera
	 * 5 edificio
	 * 6 glorieta
	 * 7 pasaje
	 * 8 polígono
	 * 9 parque
	 * 10 plaza
	 * 11 ronda
	 * 12 travesía
	 * 13 urbanización
	 * 14 vía
	 * 15 rambla
	 * 16 paseo
	 */
	protected function getTipoDeVia() {
		//[tipo_via@id=0] = avenida
	}

	/**
	 * En caso de alquiler se puede especificar
	 *
	 * 'diaria', 'semanal', 'quincenal', 'mensual',
	 * 'trimestral', 'semestral' y 'anual'.
	 *
	 * @return string
	 */
	protected function getPeriodicidad() {
		return 'mensual';
	}

	/**
	 * 'no disponible', 'a', 'b', 'c', 'd', 'e', 'f' y 'g'
	 *
	 * @return string
	 */
	protected function getCertificacionEnergetica() {
		if (!empty($this->item['ec'])) {
			return strtolower($this->item['ec']);
		}
		return 'no disponible';
	}

	/**
	 * @return array
	 */
	protected function getImages() {
		$pictures = [];
		foreach ($this->item['images'] as $counter => $image) {
			$pictures['adjunto@tipo=foto@url=' . $image] = 'Fotografia ' . ($counter+1);
		}
		return $pictures;
	}

	/**
	 * @return array
	 */
	protected function getProvinciaData() {
		$valor = $this->item['attributes']['yaencontre-city'];
		$explodedValor = explode('-', $valor);
		return [$explodedValor[3], $explodedValor[2]];
	}

	/**
	 * @return array
	 */
	protected function getPoblacionData() {
		$valor = $this->item['attributes']['yaencontre-city'];
		$explodedValor = explode('-', $valor);
		return [$explodedValor[1], $explodedValor[0]];
	}

}
