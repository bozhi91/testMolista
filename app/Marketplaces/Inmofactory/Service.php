<?php

namespace App\Marketplaces\Inmofactory;

abstract class Service extends \App\Marketplaces\Service {

	/**
	 * @param array $property
	 * @return array
	 */
	public function createProperty(array $property) {
		$url = 'https://api.inmofactory.com/api/Property/';
		$json = json_encode($property);
		$response = $this->getResponse($url, $json, 'POST');
		return $this->formatResponse($response);
	}

	/**
	 * @param array $property
	 * @return array
	 */
	public function updateProperty(array $property) {
		$url = 'https://api.inmofactory.com/api/Property/';
		$json = json_encode($property);
		$response = $this->getResponse($url, $json, 'PUT');
		return $this->formatResponse($response);
	}

	/**
	 * @param array $property
	 * @return array
	 */
	public function updatePriceOnly(array $property) {
		$url = 'https://api.inmofactory.com/api/Property/UpdatePrice';
		$json = json_encode($property);
		$response = $this->getResponse($url, $json, 'POST');
		return $this->formatResponse($response);
	}

	/**
	 * Updates a real estate property entity from "Available Status" to "Not Available".
	 * @param string|integer $propertyId
	 * @return array
	 */
	public function deleteProperty($propertyId) {
		$url = 'https://api.inmofactory.com/api/Property/' . $propertyId;
		$response = $this->getResponse($url, '', 'DELETE');
		return $this->formatResponse($response);
	}

	/**
	 * @return array
	 */
	public function getPublications() {
		$url = 'https://api.inmofactory.com/api/Publication';
		$response = $this->getResponse($url, '', 'GET');
		$stupid = json_decode($response, true);
		$people = json_decode($stupid, true);
		return $people;
	}

	/**
	 * @param string $response
	 * @return array
	 */
	private function formatResponse($response) {
		$array = json_decode($response, true);
		if (!$array) {
			$array = json_decode(str_replace("'", '"', $response), true);
		}

		return [
			($array['StatusCode'] == 200 ||
				$array['StatusCode'] == 201) ? true : false,
			['messages' => [$array['Message']]]
		];
	}

	/**
	 * @param string $url
	 * @param string $json
	 * @param string $method
	 * @return string
	 */
	private function getResponse($url, $json = '', $method) {
		$username = $this->config['username'];
		$password = $this->config['password'];

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 600); // Timeout in seconds
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$headers = array(
			'Content-Type: application/json',
			'Host: api.inmofactory.com',
		);

		if ($json) {//if json data
			$query = "'" . $json . "'"; // El json debe ir entrecomillado
			$headers[] = 'Content-Length: ' . strlen($query);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
		}

		// Save last request for debug
		$this->setLastRequest($json);

		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		// Optional Authentication:
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");

		$response = curl_exec($ch);

		curl_close($ch);

		return $response;
	}

}
