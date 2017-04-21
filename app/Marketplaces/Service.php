<?php

namespace App\Marketplaces;

abstract class Service {

	protected $config = [];
	protected $last_request;

	public function setLastRequest($request)
	{
		$this->last_request = $request;
	}

	public function getLastRequest()
	{
		return $this->last_request;
	}

	public function setConfig(array $config) {
		$this->config = $config;
	}

}
