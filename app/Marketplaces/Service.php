<?php

namespace App\Marketplaces;

use GuzzleHttp\Psr7\Response;

abstract class Service {

	protected $config = [];

	public function setConfig(array $config) {
		$this->config = $config;
	}

	/**
	 * @return Response
	 */
	abstract public function publishProperty(array $property);
}
