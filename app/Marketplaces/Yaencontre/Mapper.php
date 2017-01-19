<?php

namespace App\Marketplaces\Yaencontre;

use App\Marketplaces\Yaencontre\BaseMapper;

class Mapper extends \App\Marketplaces\Mapper {

	private $_mapper;

	/**
	 * @return array
	 */
	public function map() {
		return $this->getMapper()->map();
	}

	/**
	 * @return boolean
	 */
	public function valid() {
		return $this->getMapper()->valid();
	}

	/**
	 * @return array
	 */
	public function errors() {
		return $this->getMapper()->errors;
	}

	/**
	 * @return BaseMapper
	 */
	protected function getMapper() {
		if($this->_mapper === null) {
			if ($this->isPropertyReferencia()) {
				$this->_mapper = new ReferenciaMapper(
					$this->item
					, $this->iso_lang
					, $this->config);
			} else {
				$this->_mapper = new PromocionMapper(
					$this->item
					, $this->iso_lang
					, $this->config);
			}
		}
		return $this->_mapper;
	}
	
	/**
	 * @return bool  Property is Promosion if it's new
	 */
	public function isPropertyReferencia() {
		$property = $this->item;
		return !$property['newly_build'] || $property['second_hand'];
	}
}
