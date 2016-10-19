<?php

namespace App\Marketplaces\Yaencontre;

class Mapper extends \App\Marketplaces\Mapper {

	protected $mapper;

	/**
	 * Maps a Molista item to Yaencontre format according to
	 * @return array
	 */
	public function map() {
		if ($this->isPropertyReferencia()) {
			$mapper = new ReferenciaMapper(
					$this->item
					, $this->iso_lang
					, $this->config);
			return $mapper->map();
		}

		$mapper = new PromocionMapper(
				$this->item
				, $this->iso_lang
				, $this->config);

		return $mapper->map();
	}

	/**
	 * @return boolean
	 */
	public function valid() {
		if ($this->isPropertyReferencia()) {
			$this->mapper = new ReferenciaMapper(
					$this->item
					, $this->iso_lang
					, $this->config);
		} else {
			$this->mapper = new PromocionMapper($this->item
			, $this->iso_lang
			, $this->config);
		}

		return $this->mapper->valid();
	}

	/**
	 * @return bool
	 */
	public function isPropertyReferencia() {
		$property = $this->item;

		if (!$property['newly_build'] || $property['second_hand']) {
			return true;
		}

		//property is promocion if it's new
		return false;
	}

	public function errors()
    {
        return $this->mapper->errors;
    }

}
