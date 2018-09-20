<?php

namespace App\Marketplaces\Yaencontre;

class Yaencontre extends \App\Marketplaces\XML {

	protected $iso_lang = 'es';
	protected $configuration = [
		[
			'block' => 'contact_data',
			'fields' => [
				[
					'name' => 'oficina',
					'type' => 'text',
					'required' => true
				],
			]
		]
	];

	public function getPropertiesXML(array $properties) {
		$this->writer = static::getWriter($this->config);

		if (method_exists($this->writer, 'start')) {
			$this->writer->start();
		}

		//Sort properties
		$sortedProperties = $this->sortProperties($properties);
		unset($properties);

		//Write referencias
		$this->writer->startReferencias();
		foreach ($sortedProperties['referencias'] as $p) {
			$this->mapProperty($p);
		}
		$this->writer->endElement();

		//Write promociones
		$this->writer->startPromociones();
		foreach ($sortedProperties['promociones'] as $p) {
			$this->mapProperty($p);
		}
		$this->writer->endElement();

		return $this->writer->getXml();
	}

	/**
	 * Will map single property
	 * @param array $p
	 */
	private function mapProperty(array $p) {
		$mapper = static::getMapper($p, $this->iso_lang, $this->config);
		if ($mapper->valid()) {
			$timezone = new \DateTimeZone(\Config::get('app.timezone'));
			$datetime = new \DateTime($p['updated_at'], $timezone);
			$timestamp = $datetime->format("U");

			$tagName = $mapper->isPropertyReferencia() ? 'referencia' : 'promocion';
			$tagName .= '@id=' . $p['reference'] . '@timestamp=' . $timestamp;

			$this->writer->addItem([$tagName => $mapper->map()]);
		}
	}

	/**
	 * Put properties into categories (array keys):
	 * - referencias
	 * - promociones
	 *
	 * @param array $properties
	 * @return array
	 */
	protected function sortProperties(array $properties) {
		$sortedProperties = [];
		$sortedProperties['referencias'] = [];
		$sortedProperties['promociones'] = [];

		foreach ($properties as $p) {
			$mapper = static::getMapper($p, $this->iso_lang, $this->config);
			if ($mapper->isPropertyReferencia()) {
				$sortedProperties['referencias'][] = $p;
			} else {
				$sortedProperties['promociones'][] = $p;
			}
		}
		return $sortedProperties;
	}

	/**
	 * @return array
	 */
	public function getAttributes() {
		return (new AttributesHandler())->getAttributes();
	}

	/**
	 * @return string|null
	 */
	public function getFeedUrl() {
		if (!empty($this->config['oficina'])) {
			return $this->config['oficina'] . '.xml';
		}
	}

}
