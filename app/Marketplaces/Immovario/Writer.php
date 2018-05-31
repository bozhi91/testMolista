<?php

namespace App\Marketplaces\Immovario;

class Writer extends \App\XML\Writer {

	protected $ended = false;

	public function __construct() {
		$this->openMemory();
		$this->startDocument('1.0', 'UTF-8');

        $this->startElement('Document');
        $this->startElement('Properties');
		$this->writeAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
		$this->writeAttribute('xmlns:xsd', 'http://www.w3.org/2001/XMLSchema');
	}

	public function end() {
		$this->endElement();
		$this->endDocument();

		$this->ended = true;
	}

	public function addItem($item) {
		$this->write('Property', $item);
	}

	public function getXml() {
		if (!$this->ended) {
			$this->end();
		}
		return parent::getXml();
	}
}
