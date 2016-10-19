<?php

namespace App\Marketplaces\Yaencontre;

class Writer extends \App\XML\Writer {

	protected $ended = false;
	protected $config;

	public function __construct() {
		$this->openMemory();
		$this->startDocument('1.0', 'UTF-8');
	}

	public function setConfig(array $config) {
		$this->config = $config;
	}

	public function start() {
		$this->startElement('publicacion');
		$this->writeAttribute('oficina', $this->config['oficina']);
	}

	public function startReferencias() {
		$this->startElement('referencias');
	}

	public function startPromociones() {
		$this->startElement('promociones');
	}
		
	public function end() {
		$this->endElement();
		$this->endDocument();

		$this->ended = true;
	}

	public function addItem($item) {
		foreach ($item as $key => $value) {
			$this->write($key, $value);
		}
	}

	public function getXml() {
		if (!$this->ended) {
			$this->end();
		}

		return parent::getXml();
	}

}
