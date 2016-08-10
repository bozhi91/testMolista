<?php

namespace App\Marketplaces\ReporteInmobiliario;

class Writer extends \App\XML\Writer {

	protected $ended = false;

	public function __construct() {
		$this->openMemory();
		$this->startDocument('1.0', 'UTF-8');
		$this->startElement('ads');
	}

	public function end() {
		$this->endElement();
		$this->endDocument();

		$this->ended = true;
	}

	public function addItem($item) {
		$this->write('ad', $item);
	}

	public function getXml() {
		if (!$this->ended) {
			$this->end();
		}

		return parent::getXml();
	}

}
