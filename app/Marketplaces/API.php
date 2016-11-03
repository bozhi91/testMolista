<?php

namespace App\Marketplaces;

use App\Marketplaces\Service;
use App\Marketplaces\Interfaces\PublishPropertyApiInterface;

abstract class API extends Base implements PublishPropertyApiInterface {

	private $_service;

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
