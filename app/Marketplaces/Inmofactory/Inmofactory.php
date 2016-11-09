<?php

namespace App\Marketplaces\Inmofactory;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

abstract class Inmofactory extends \App\Marketplaces\API {

	private $_client;

	/**
	 * @return Client
	 */
	protected function getClient() {
		if ($this->_client === null) {
			$userAndPasswordBase64Encoded = base64_encode("tesapimolista@mail.es:RGfAU1Z");			
			$this->_client = new Client([
				'headers' => ["Authorization" => "Basic " . $userAndPasswordBase64Encoded]
			]);
		}
		return $this->_client;
	}

	public function validateProperty(array $property) {
		
	}

	public function getProperty(){
		
	}
	
	/**
	 * @return mixed Returns all the agency publications sites.
	 */
	public function getPublicationSites() {
		$url = 'https://api.inmofactory.com/api/Property/';
		$username = "tesapimolista@mail.es";
		$password = "RGfAU1Z";    

		$bla = [
			"ExternalId" => "APIDEMO_09",
			"AgencyReference" => "APIDEMO_01",
			"AgencyId" => 103518,
			"TypeId" => 1,
			"SubtypeId" => 0,
			"IsNewConstruction" => false,
			"PropertyStatusId" => 1,
			"ExpirationCauseId" => 0,
			"ShowSurface" => true,
			"ContactTypeId" => 1,
			"IsPromotion" => false,
			"BankAwardedId" => 1,
			"ContactName" => "Administrador",
			"PropertyAddress" => [
				[
					"ZipCode" => "08225",
					"CountryId" => 724,
					"Zone" => "Terrassa, Zona de - Terrassa",
					"StreetTypeId" => 1,
					"Street" => "Nombre de la calle",
					"Number" => "10",
					"FloorId" => 0,
					"x" => 2.0035664770888,
					"y" => 41.571569662267,
					"VisibilityModeId" => 3
				]
			],
			"PropertyDocument" => [

				[
					"TypeId" => 1,
					"description" => "Foto de la cocina",
					"Url" => "https://www.gstatic.com/webp/gallery3/1.png",
					"RoomTypeId" => 7,
					"FileTypeId" => 2,
					"Visible" => true,
					"SortingId" => 1
				],
				[
					"TypeId" => 1,
					"description" => "Foto del baño",
					"Url" => "https://www.gstatic.com/webp/gallery3/2.png",
					"RoomTypeId" => 7,
					"FileTypeId" => 2,
					"Visible" => true,
					"SortingId" => 1
				]
			],
			"PropertyFeature" => [
				[
					"FeatureId" => 30,
					"LanguageId" => 4,
					"BoolValue" => true
				],
				[
					"FeatureId" => 1,
					"LanguageId" => 4,
					"DecimalValue" => 100
				],
				[
					"FeatureId" => 4,
					"LanguageId" => 4,
					"BoolValue" => true
				],
				[
					"FeatureId" => 31,
					"LanguageId" => 4,
					"DecimalValue" => 1
				],
				[
					"FeatureId" => 33,
					"LanguageId" => 4,
					"DecimalValue" => 1
				],
				[
					"FeatureId" => 98,
					"LanguageId" => 4,
					"DecimalValue" => 1
				],
				[
					"FeatureId" => 11,
					"LanguageId" => 4,
					"DecimalValue" => 4
				],
				[
					"FeatureId" => 12,
					"LanguageId" => 4,
					"DecimalValue" => 3
				]
			],
			"PropertyUser" => [
				[
					"UserId" => 170886,
					"IsPrincipal" => true
				]
			],
			"PropertyContactInfo" => [
				[
					"TypeId" => 2,
					"Value" => "000000000",
					"ValueTypeId" => 3
				]
			],
			"PropertyFeatureGroupComment" => [
				[
					"FeatureGroupId" => 2,
					"LanguageId" => 4,
					"Comments" => ""
				],
				[
					"FeatureGroupId" => 15,
					"LanguageId" => 4,
					"Comments" => ""
				],
				[
					"FeatureGroupId" => 3,
					"LanguageId" => 4,
					"Comments" => ""
				],
				[
					"FeatureGroupId" => 5,
					"LanguageId" => 4,
					"Comments" => ""
				]
			],
			"PropertyCustomer" => [
				[
					"CustomerId" => 0000000,
					"IsPrincipal" => false
				]
			],
			"PropertyPublications" => [
				[
					"PublicationId" => 1,
					"PublicationTypeId" => 2
				],
				[
					"PublicationId" => 1,
					"PublicationTypeId" => 2
				],
			],
			"PropertyTransaction" => [
				[
					"TransactionTypeId" => 1, //VENTA 
					"CustomerPrice" => 0,
					"Price" => 1500,
					"PriceM2" => 15,
					"CurrencyId" => 1,
					"PaymentPeriodicityId" => 6,
					"ShowPrice" => true
				]
			]
		];

		$json = json_encode($bla);
		
		$query = "'".$json."'"; // El json debe ir entrecomillado

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 600); // Timeout in seconds        

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Host: api.inmofactory.com',
            'Content-Length: ' . strlen($query)
        ));

        // Optional Authentication:
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");

        $response = curl_exec($ch);

        curl_close($ch);

        return $response;
		
		
		
		
		
		//$endpoint = 'https://api.inmofactory.com/api/publication';
		//$res = $this->getClient()->request('GET', $endpoint);
		//return json_decode($res->getBody());
	}

	
	
	
	/**
	 * @param array $property
	 * @return array
	 */
	public function publishProperty(array $property) {



		$bla = [
			"ExternalId" => "APIDEMO_01",
			"AgencyReference" => "APIDEMO_01",
			"AgencyId" => 103518,
			"TypeId" => 1,
			"SubtypeId" => 0,
			"IsNewConstruction" => false,
			"PropertyStatusId" => 1,
			"ExpirationCauseId" => 0,
			"ShowSurface" => true,
			"ContactTypeId" => 1,
			"IsPromotion" => false,
			"BankAwardedId" => 1,
			"ContactName" => "Administrador",
			"PropertyAddress" => [
				[
					"ZipCode" => "08225",
					"CountryId" => 724,
					"Zone" => "Terrassa, Zona de - Terrassa",
					"StreetTypeId" => 1,
					"Street" => "Nombre de la calle",
					"Number" => "10",
					"FloorId" => 0,
					"x" => 2.0035664770888,
					"y" => 41.571569662267,
					"VisibilityModeId" => 3
				]
			],
			"PropertyDocument" => [

				[
					"TypeId" => 1,
					"description" => "Foto de la cocina",
					"Url" => "https://www.gstatic.com/webp/gallery3/1.png",
					"RoomTypeId" => 7,
					"FileTypeId" => 2,
					"Visible" => true,
					"SortingId" => 1
				],
				[
					"TypeId" => 1,
					"description" => "Foto del baño",
					"Url" => "https://www.gstatic.com/webp/gallery3/2.png",
					"RoomTypeId" => 7,
					"FileTypeId" => 2,
					"Visible" => true,
					"SortingId" => 1
				]
			],
			"PropertyFeature" => [
				[
					"FeatureId" => 30,
					"LanguageId" => 4,
					"BoolValue" => true
				],
				[
					"FeatureId" => 1,
					"LanguageId" => 4,
					"DecimalValue" => 100
				],
				[
					"FeatureId" => 4,
					"LanguageId" => 4,
					"BoolValue" => true
				],
				[
					"FeatureId" => 31,
					"LanguageId" => 4,
					"DecimalValue" => 1
				],
				[
					"FeatureId" => 33,
					"LanguageId" => 4,
					"DecimalValue" => 1
				],
				[
					"FeatureId" => 98,
					"LanguageId" => 4,
					"DecimalValue" => 1
				],
				[
					"FeatureId" => 11,
					"LanguageId" => 4,
					"DecimalValue" => 4
				],
				[
					"FeatureId" => 12,
					"LanguageId" => 4,
					"DecimalValue" => 3
				]
			],
			"PropertyUser" => [
				[
					"UserId" => 170886,
					"IsPrincipal" => true
				]
			],
			"PropertyContactInfo" => [
				[
					"TypeId" => 2,
					"Value" => "000000000",
					"ValueTypeId" => 3
				]
			],
			"PropertyFeatureGroupComment" => [
				[
					"FeatureGroupId" => 2,
					"LanguageId" => 4,
					"Comments" => ""
				],
				[
					"FeatureGroupId" => 15,
					"LanguageId" => 4,
					"Comments" => ""
				],
				[
					"FeatureGroupId" => 3,
					"LanguageId" => 4,
					"Comments" => ""
				],
				[
					"FeatureGroupId" => 5,
					"LanguageId" => 4,
					"Comments" => ""
				]
			],
			"PropertyCustomer" => [
				[
					"CustomerId" => 0000000,
					"IsPrincipal" => false
				]
			],
			"PropertyPublications" => [
				[
					"PublicationId" => 1,
					"PublicationTypeId" => 2
				],
				[
					"PublicationId" => 1,
					"PublicationTypeId" => 2
				],
			],
			"PropertyTransaction" => [
				[
					"TransactionTypeId" => 1, //VENTA 
					"CustomerPrice" => 0,
					"Price" => 1500,
					"PriceM2" => 15,
					"CurrencyId" => 1,
					"PaymentPeriodicityId" => 6,
					"ShowPrice" => true
				]
			]
		];

		return json_encode($bla);

		

		return $this->getClient()->request('POST', 'https://api.inmofactory.com/api/property', [
				'headers' => ['Content-Type' => 'application/json'],
				'body' => json_encode($bla),
		]);
	}

	public function unpublishProperty(array $property) {
		;
	}

	public function updateProperty(array $property) {
		;
	}

}
