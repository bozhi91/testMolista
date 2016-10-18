<?php

namespace App\Marketplaces\FlatAlert;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

class Service extends \App\Marketplaces\Service {

	private $_client;
	
	/**
	 * @return Client
	 */
	public function getClient() {
		if ($this->_client === null) {
			$this->_client = new Client([
				'base_uri' => 'http://178.62.118.66:5008/api/v1/priv/'
			]);
		}
		return $this->_client;
	}

	/**
	 * @param array $property
	 * @return Response
	 */
	public function publishProperty(array $property) {		
		return $this->getClient()->post('newProperty', [
			'form_params' => [
				'access_token' => $this->config['access_token'],
				'access_token_secret' => $this->config['access_token_secret'],
				'data' => $property
			]
		]);
	}

	/**
	 * @param array $property
	 * @return Response
	 */
	public function updateProperty(array $property) {
		return $this->getClient()->post('updateProperty', [
			'form_params' => [
				'access_token' => $this->config['access_token'],
				'access_token_secret' => $this->config['access_token_secret'],
				'data' => $property
			]
		]);
	}
	
	/**
	 * @param string $customer_property_id
	 * @return Response
	 */
	public function checkIfExists($customer_property_id) {
		return $this->getClient()->post('checkPropertyExist', [
			'form_params' => [
				'access_token' => $this->config['access_token'],
				'access_token_secret' => $this->config['access_token_secret'],
				'data' => ['customer_property_id' => $customer_property_id]
			]
		]);
	}
	
}
