<?php

namespace App\Marketplaces\FlatAlert;

use GuzzleHttp\Exception\RequestException;

class FlatAlert extends \App\Marketplaces\API {

	protected $iso_lang = 'es';
	protected $configuration = [
		[
			'block' => 'access_data',
			'fields' => [
				[
					'name' => 'access_token',
					'type' => 'text',
					'required' => true
				],
				[
					'name' => 'access_token_secret',
					'type' => 'text',
					'required' => true
				],
			]
		],
		[
			'block' => 'contact_data',
			'fields' => [
				[
					'name' => 'email',
					'type' => 'text',
					'required' => false
				],
				[
					'name' => 'phone',
					'type' => 'text',
					'required' => false
				],
			]
		],
	];

	/**
	 * @param array $property
	 * @return array
	 */
	public function publishProperty(array $property) {
		$mapper = static::getMapper($property, $this->iso_lang, $this->config);

		if (!$mapper->valid()) {
			return [false, ['messages' => $mapper->errors]];
		}

		try {
			$response = $this->getService()->publishProperty($mapper->map());
			return $this->getResponse($response);
		} catch (RequestException $e) {
			return $this->getExceptionResponse($e);
		}
	}

	/**
	 * @param array $property
	 * @return array
	 */
	public function updateProperty(array $property) {
		$mapper = static::getMapper($property, $this->iso_lang, $this->config);

		if (!$mapper->valid()) {
			return [false, ['messages' => $mapper->errors]];
		}

		try {
			$response = $this->getService()->publishProperty($mapper->map());
			return $this->getResponse($response);
		} catch (RequestException $e) {
			return $this->getExceptionResponse($e);
		}
	}

	/**
	 * @param array $property
	 * @return array
	 */
	public function unpublishProperty(array $property) {
		$mapper = static::getMapper($property, $this->iso_lang, $this->config);

		if (!$mapper->valid()) {
			return [false, ['messages' => $mapper->errors]];
		}

		try {
			$mapped = $mapper->map();
			$mapped['status'] = '99';
			$response = $this->getService()->publishProperty($mapped);
			return $this->getResponse($response);
		} catch (RequestException $e) {
			return $this->getExceptionResponse($e);
		}
	}

	/**
	 * @param RequestException $e
	 * @return array
	 */
	protected function getExceptionResponse($e) {
		$body = $e->getResponse()->getBody();
		$response = json_decode($body, true);
		$message = $this->getMessage($response['message']);
		return [false, ['messages' => [$message]]];
	}

	/**
	 * @param Response $res
	 */
	protected function getResponse($res){
		$body = $res->getBody();
		$response = json_decode($body, true);
		if($response['code']){
			return [true, ['messages' => [$response['message']]]];
		} else {
			return [false, ['messages' => [$response['message']]]];
		}
	}
	
	/**
	 * FlatAlert, por favor. No dejan el mensaje en la resputa. Lo dejan en el word.
	 * @param integer $messageCode
	 * @return string
	 */
	protected function getMessage($messageCode) {
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
