<?php

namespace App\Marketplaces\Habitaclia;

class Writer extends \App\XML\Writer {

	protected $ended = false;

	public function __construct() {
		$this->openMemory();
		$this->startDocument('1.0', 'UTF-8');
		$this->startElement('producto');
		$this->writeAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
		$this->writeAttribute('xmlns:xsd', 'http://www.w3.org/2001/XMLSchema');
	}

	public function end() {
		$this->endElement();
		$this->endDocument();

		$this->ended = true;
	}

	public function addItem($item) {
		$this->write('inmueble', $item);
	}

	public function getXml() {
		if (!$this->ended) {
			$this->end();
		}

		return parent::getXml();
	}

}
