<?php

namespace App\Marketplaces;

use App\Marketplaces\Interfaces\PublishPropertyApiInterface;

abstract class API extends Base implements PublishPropertyApiInterface {

	private $_service;

	/**
	 * @return Service
	 */
	public function getService() {
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
