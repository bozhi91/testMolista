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
				'base_uri' => 'http://dev.flatalert.es:5008/api/v1/priv/'
			]);
		}
		return $this->_client;
	}

	/**
	 *
	 * @param array $property
	 * @return array
	 */
	public function createProperty(array $property) {
		$res = $this->getClient()->post('newProperty', [
			'form_params' => [
				'access_token' => $this->config['access_token'],
				'access_token_secret' => $this->config['access_token_secret'],
				'data' => $property
			]
		]);
		return $this->formatResponse($res);
	}

	/**
	 * @param array $property
	 * @return array
	 */
	public function updateProperty(array $property) {
		$res = $this->getClient()->post('updateProperty', [
			'form_params' => [
				'access_token' => $this->config['access_token'],
				'access_token_secret' => $this->config['access_token_secret'],
				'data' => $property
			]
		]);
		return $this->formatResponse($res);
	}

	/**
	 * @param string $customer_property_id
	 * @return boolean
	 */
	public function checkPropertyExist($customer_property_id) {
		$res = $this->getClient()->post('checkPropertyExist', [
			'form_params' => [
				'access_token' => $this->config['access_token'],
				'access_token_secret' => $this->config['access_token_secret'],
				'data' => ['customer_property_id' => $customer_property_id]
			]
		]);

		$existBody = $res->getBody();
		$decoded = json_decode($existBody, true);
		return $decoded['code'] && $decoded['message'] == '22'; //22 exists
	}

	/**
	 * @param Response $res
	 * @return array
	 */
	public function formatResponse($res) {
		$body = $res->getBody();
		$response = json_decode($body, true);
		$message = $this->getMessage($response['message']);

		return [
			$response['code'] ? true : false,
			['messages' => [$message]]
		];
	}

	/**
	 * FlatAlert, por favor. No dejan el mensaje en la resputa. Lo dejan en el word.
	 * @param integer $messageCode
	 * @return string
	 */
	public function getMessage($messageCode) {
		switch ($messageCode) {
			case 20: return "Credenciales del cliente no válidas";
			case 21: return "No se ha podido obtener la posición GPS";
			case 22: return "Ya existe un inmueble con ese ID";
			case 23: return "No existe un inmueble con ese ID";
			case 26: return "Ya existe el inmueble";
			case 27: return "Faltan datos de la dirección";
			case 28: return "Images no es un array válido";
			case 29: return "balcony, elevator, furnished equipped deben ser boolean";
			case 30: return "Debe especificar una descripción para la certificación energética";
			case 31: return "data no es un JSON válido";
			case 32: return "No existe un inmueble que cumpla esa condición";
			case 33: return "No se ha publicado el inmueble porque no hay imagenes válidas";
			case 34: return "Plans no es un array válido";
			case 35: return "energy_certification no es un JSON válido";
			case 58: return "Debe especificar el área del inmueble";
			case 59: return "Status del inmueble no válido";
			case 62: return "Datos incorrectos";
			case 63: return "URLS en energy_certification no es un array válido";
			case 64: return "Ya existe un inmueble en esa ubicación";
			default: return "Unknown error: $messageCode";
		}
	}

}
