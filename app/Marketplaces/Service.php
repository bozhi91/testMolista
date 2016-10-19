<?php

namespace App\Marketplaces;

abstract class Service {

	protected $config = [];

	public function setConfig(array $config) {
		$this->config = $config;
	}

}
