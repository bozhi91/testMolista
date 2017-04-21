<?php

namespace App\Marketplaces;

use App\Marketplaces\Service;
use App\Marketplaces\Interfaces\PublishPropertyApiInterface;

abstract class API extends Base implements PublishPropertyApiInterface {

	private $_service;
	protected $last_request;

	public function setLastRequest($request)
	{
		$this->last_request = $request;
	}

	public function getLastRequest()
	{
		return $this->last_request;
	}

	/**
	 * @param array $config
	 * @return Service
	 */
	protected function getService() {
		if ($this->_service === null) {
			$class = static::getClassName() . '\Service';
			$instance = new $class;
			if (method_exists($instance, 'setConfig')) {
				$instance->setConfig($this->config);
			}
			$this->_service = $instance;
		}
		return $this->_service;
	}

}
