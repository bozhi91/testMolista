<?php

namespace App\Marketplaces\Idealista;

use App\Marketplaces\Base;
use App\Marketplaces\Interfaces\PublishPropertyXmlInterface;

class Idealista extends Base implements PublishPropertyXmlInterface {

	protected $iso_lang = 'es';
	protected $aggregator;
	protected $code;
	protected $reference;
	protected $newly_build;
	//Original properties data
	protected $properties = [];
	//Data client
	protected $client = [];
	//Formated secondhand data
	protected $secondhandListing = [];
	//Formated newbuild data
	protected $newbuildListing = [];

	public function __construct(array $config = []) {
		parent::__construct($config);

		//Set config params
		$this->setAggregator($config['aggregator']);
		$this->setCode(@$config['code']);
		$this->newly_build = !empty($config['newly_build']);

		//Generate client node
		$this->setClient([
			'aggregator' => $this->getAggregator(),
			'code' => $this->getCode(),
			'reference' => null
		]);
	}

	public function validateProperty(array $property) {
		return true;
	}

	//Process all data and generate XML
	public function getPropertiesXML(array $properties) {

		$this->load($properties);
		return $this->generateXML();
	}

	//Load and process all data
	public function load(array $properties) {

		//Set properties param
		$this->setProperties($properties);

		//Process properties array
		$this->processProperties();
	}

	//Generate XML string
	protected function generateXML() {

		//Start xml doc
		$writer = new Writer();
		$writer->openMemory();
		$writer->setIndent(true);
		$writer->startDocument('1.0', 'UTF-8');
		/* <?xml version="1.0" encoding="UTF-8"?> */


		//Clients start
		$writer->startElement('clients');
		$writer->startElement('client');

		//Data client
		$writer->write($this->getClient());

		//Write secondhandListing
		$writer->startElement('secondhandListing');
		foreach ($this->getSecondhandListing() as $k => $data) {
			$writer->startElement('property');
			$this->writeXmlProperty($data, $writer);
			$writer->endElement();
		}
		$writer->endElement();

		//Write newbuildListing
		if (!empty($this->newly_build)) {
			$writer->startElement('newbuildListing');
			foreach ($this->getNewbuildListing() as $k => $data) {
				//Start newDevelopment path
				$writer->startElement('newDevelopment');

				//Code
				$writer->startElement('code');
				$writer->write($data['promo_code']);
				$writer->endElement();

				//Address
				$writer->startElement('address');
				$writer->write($data['address']);
				$writer->endElement();

				//Features path
				$writer->startElement('features');
				$writer->writeAttribute('type', 'promo');
				$writer->endElement();

				//Typologies path
				$writer->startElement('typologies');
				//Add property
				$writer->startElement('property');
				$this->writeXmlProperty($data, $writer);
				$writer->endElement();
				//End typologies path
				$writer->endElement();

				//End newDevelopment path
				$writer->endElement();
			}
			$writer->endElement();
		}

		//Clients end
		$writer->endElement();
		$writer->endElement();


		$request = $writer->outputMemory();
		return $request;
	}

	//Write XML property
	protected function writeXmlProperty($data, &$writer) {
		//Separate operation
		$operation = $data['operation_type'];
		$operation_type = $data['mode'];
		unset($data['operation_type']);
		unset($data['mode']);

		//Separate features
		$features = $data['features'];
		$features_type = $data['type'];
		unset($data['features']);
		unset($data['type']);

		$writer->write($data);


		//Write operation
		$writer->startElement('operation');
		$writer->writeAttribute('type', $operation_type);
		$writer->write($operation);
		$writer->endElement();

		//Write features
		$writer->startElement('features');
		$writer->writeAttribute('type', $features_type);
		$writer->write($features);
		$writer->endElement();
	}

	protected function processProperties() {
		$properties = $this->getProperties();
		if (empty($properties))
			$properties = [];

		foreach ($properties as $k => $property) {
			$this->addProperty($property);
		}
	}

	//Add property to secondhandListing or newbuildListing props
	protected function addProperty(array $property) {

		$type = $this->translateType($property['type']);

		$propertyIdealista = [
			'code' => $property['id'],
			'reference' => $property['reference'],
			'scope' => 1, //Idealista & Microsite
			'address' => $this->processAddress($property),
			'features' => $this->processFeatures($property, $type),
			'operation_type' => $this->processOperation($property),
			'descriptions' => $this->processDescriptions($property),
			'links' => $this->processLinks($property),
			'images' => $this->processImages($property),
			'mode' => $property['mode'],
			'type' => $type,
		];


		if (empty($property['newly_build'])) {
			$this->secondhandListing[] = $propertyIdealista;
		} else {
			$propertyIdealista['promo_code'] = $property['site_id'] . '_' . $property['id'];
			$this->newbuildListing[] = $propertyIdealista;
		}
	}

	//Property type translate format
	protected function translateType($type) {

		switch ($type) {
			case 'chalet':
			case 'house':
			case 'villa':
			case 'terraced_house':
				$idealista_type = 'house';
				break;
			case 'farmhouse':
				$idealista_type = 'countryHouse';
				break;
			case 'store':
			case 'industrial':
				$idealista_type = 'premise';
				break;
			case 'lot':
			case 'state':
				$idealista_type = 'land';
				break;
			case 'apartment':
			case 'duplex':
			case 'penthouse':
			default:
				$idealista_type = 'flat';
				break;
		}

		return $idealista_type;
	}

	//Build images format
	protected function processImages($property) {

		$images['image'] = [];
		foreach ($property['images'] as $k => $url) {
			$images['image'][] = [
				'code' => 0,
				'url' => $url
			];
		}

		return $images;
	}

	//Build descriptions format
	protected function processDescriptions($property) {

		$descriptions['description'] = [];

		foreach ($property['title'] as $locale => $title) {
			$language = $this->translateLanguage($locale);

			if ($language) {
				$descriptions['description'][] = [
					'language' => $language,
					'title' => $title,
					'comment' => mb_substr(@$property['description'][$locale], 0, 2499),
				];
			}
		}

		return $descriptions;
	}

	//Build links format
	protected function processLinks($property) {

		$links['link'] = [];


		foreach ($property['url'] as $locale => $link) {
			$language = $this->translateLanguage($locale);

			if ($language) {
				$links['link'][] = [
					'language' => $language,
					'comment' => '',
					'url' => $link,
				];
			}
		}

		return $links;
	}

	/**
	 * @param string $locale
	 * @return int|null
	 */
	protected function translateLanguage($locale) {
		switch ($locale) {
			case 'es': return 1;
			case 'en': return 2;
			case 'fr': return 3;
			case 'de': return 4;
			case 'pt': return 5;
			case 'it': return 6;
			case 'ca': return 7;
			case 'ru': return 8;
			default: return null;
		}
	}

	//Build operation format
	protected function processOperation(array $property) {

		$operationType = [
			'price' => $property['price'],
		];

		return $operationType;
	}

	/**
	 * @param array $property
	 * @param string $type
	 * @return array
	 */
	protected function processFeatures(array $property, $type) {
		if ($type == 'flat') {
			return $this->getFlatFeatures($property);
		}elseif($type == 'house'){
			return $this->getHouseFeauters($property);
		}elseif($type == 'countryHouse'){
			return $this->getCountryHouseFeauters($property);
		}elseif($type == 'premise'){
			return $this->getPremiseFeauters($property);
		}elseif($type == 'land'){
			return $this->getLandFeauters($property);
		}
	}

	/**
	 * @param array $property
	 * @return array
	 */
	private function getFlatFeatures(array $property) {
		$oFeatures = $property['features'];

		$features = [
			'bathrooms' => $property['baths'],
			'bedrooms' => $property['bedrooms'],
			'constructedArea' => $property['size'],
			'energyCertification' => [
			    'rating' => $this->getEnergyCertificationRating($property)
			],
			'conditionedAir' => empty($oFeatures['air-conditioning']) ? 1 : 2,
			'alarm' => (empty($oFeatures['alarm'])) ? 'false' : 'true',
			'balconyNumber' => empty($oFeatures['balcony']) ? 'false' : 'true',
			'elevator' => empty($oFeatures['elevator']) ? 'false' : 'true',
			'furniture' => (empty($oFeatures['furnished'])) ? 1 : 3,
			'garden' => empty($oFeatures['garden']) ? 'false' : 'true',
			'parkingSpacesInPrice' => (empty($oFeatures['parking']) && empty($oFeatures['garage'])) ? 'false' : 'true',
			'pool' => empty($oFeatures['pool']) ? 'false' : 'true',
			//'lastFloor' => empty($oFeatures['atic']) ? 'false' : 'true',
			'windowsLocation' => (empty($oFeatures['exterior'])) ? 2 : 1
		];

		if (!empty($property['rooms'])) {
			$features['rooms'] = $property['rooms'];
		}
		//Special fields if don't have don't appear
		if (!empty($oFeatures['terrase'])) {
			$features['terraceType'] = 0;
		}
		if (!empty($oFeatures['heating'])) {
			$features['heatingType'] = 0;
		}

		return $features;
	}

	/**
	 * @param array $property
	 * @return array
	 */
	private function getHouseFeauters(array $property){
		$oFeatures = $property['features'];

		$features = [
			'bathrooms' => $property['baths'],
			'bedrooms' => $property['bedrooms'],
			'constructedArea' => $property['size'],
			'energyCertification' => [
			    'rating' => $this->getEnergyCertificationRating($property)
			],
			'conditionedAir' => empty($oFeatures['air-conditioning']) ? 1 : 2,
			'alarm' => (empty($oFeatures['alarm'])) ? 'false' : 'true',
			'balconyNumber' => empty($oFeatures['balcony']) ? 'false' : 'true',
			'furniture' => (empty($oFeatures['furnished'])) ? 1 : 3,
			'garden' => empty($oFeatures['garden']) ? 'false' : 'true',
			'parkingSpacesInPrice' => (empty($oFeatures['parking']) && empty($oFeatures['garage'])) ? 'false' : 'true',
			'pool' => empty($oFeatures['pool']) ? 'false' : 'true',
		];

		if (!empty($property['rooms'])) {
			$features['rooms'] = $property['rooms'];
		}
		//Special fields if don't have don't appear
		if (!empty($oFeatures['terrase'])) {
			$features['terraceType'] = 0;
		}
		if (!empty($oFeatures['heating'])) {
			$features['heatingType'] = 0;
		}

		return $features;
	}


	/**
	 * @param array $property
	 * @return array
	 */
	private function getCountryHouseFeauters(array $property){
		$oFeatures = $property['features'];

		$features = [
			'bathrooms' => $property['baths'],
			'bedrooms' => $property['bedrooms'],
			'constructedArea' => $property['size'],
			'energyCertification' => [
			    'rating' => $this->getEnergyCertificationRating($property)
			],
			'conditionedAir' => empty($oFeatures['air-conditioning']) ? 1 : 2,
			'alarm' => (empty($oFeatures['alarm'])) ? 'false' : 'true',
			'balconyNumber' => empty($oFeatures['balcony']) ? 'false' : 'true',
			'furniture' => (empty($oFeatures['furnished'])) ? 1 : 3,
			'garden' => empty($oFeatures['garden']) ? 'false' : 'true',
			'parkingSpacesInPrice' => (empty($oFeatures['parking']) && empty($oFeatures['garage'])) ? 'false' : 'true',
			'pool' => empty($oFeatures['pool']) ? 'false' : 'true',
		];

		if (!empty($property['rooms'])) {
			$features['rooms'] = $property['rooms'];
		}
		//Special fields if don't have don't appear
		if (!empty($oFeatures['terrase'])) {
			$features['terraceType'] = 0;
		}
		if (!empty($oFeatures['heating'])) {
			$features['heatingType'] = 0;
		}

		return $features;
	}

	/**
	 * @param array $property
	 * @return array
	 */
	private function getPremiseFeauters(array $property){
		$oFeatures = $property['features'];

		$features = [
			'constructedArea' => $property['size'],
			'energyCertification' => [
			    'rating' => $this->getEnergyCertificationRating($property)
			],
			'conditionedAir' => empty($oFeatures['air-conditioning']) ? 1 : 2,
			'alarm' => (empty($oFeatures['alarm'])) ? 'false' : 'true',
		];

		if (!empty($property['rooms'])) {
			$features['rooms'] = $property['rooms'];
		}

		if(!empty($property['baths'])){
			$features['bathrooms'] = $property['baths'];
		}

		if (!empty($oFeatures['heating'])) {
			$features['heatingType'] = 0;
		}

		return $features;
	}

	/**
	 * @param array $property
	 * @return array
	 */
	private function getLandFeauters(array $property){
		$oFeatures = $property['features'];

		$features = [
			'plotArea' => $property['size'],
			'landType' => 0,
		];

		return $features;
	}

	//Build address format
	protected function processAddress(array $property) {

		$location = $property['location'];

		$address = [
			'visibility' => empty($location['show_address']) ? 3 : 1,
			'country' => $location['country'],
			'streetName' => empty($location['address_parts']['street']) ? $location['address'] : $location['address_parts']['street'],
			'streetNumber' => empty($location['address_parts']['number']) ? '' : $location['address_parts']['number'],
			'cityName' => $location['city'],
			'postalcode' => $location['zipcode'],
			'floor' => '',
			'coordinates' => [
				'precision' => 1,
				'latitude' => $location['lat'],
				'longitude' => $location['lng'],
			]
		];

		return $address;
	}

	/**
	 * @return mixed
	 */
	public function getAggregator() {
		return $this->aggregator;
	}

	/**
	 * @param mixed $aggregator
	 */
	public function setAggregator($aggregator) {
		$this->aggregator = $aggregator;
	}

	/**
	 * @return mixed
	 */
	public function getCode() {
		return $this->code;
	}

	/**
	 * @param mixed $code
	 */
	public function setCode($code) {
		$this->code = $code;
	}

	/**
	 * @return mixed
	 */
	public function getReference() {
		return $this->reference;
	}

	/**
	 * @param mixed $reference
	 */
	public function setReference($reference) {
		$this->reference = $reference;
	}

	/**
	 * @return array
	 */
	public function getProperties() {
		return $this->properties;
	}

	/**
	 * @param array $properties
	 */
	public function setProperties($properties) {
		$this->properties = $properties;
	}

	/**
	 * @return array
	 */
	public function getClient() {
		return $this->client;
	}

	/**
	 * @param array $client
	 */
	public function setClient($client) {
		$this->client = $client;
	}

	/**
	 * @return array
	 */
	public function getSecondhandListing() {
		return $this->secondhandListing;
	}

	/**
	 * @param array $secondhandListing
	 */
	public function setSecondhandListing($secondhandListing) {
		$this->secondhandListing = $secondhandListing;
	}

	/**
	 * @return array
	 */
	public function getNewbuildListing() {
		return $this->newbuildListing;
	}

	/**
	 * @param array $newbuildListing
	 */
	public function setNewbuildListing($newbuildListing) {
		$this->newbuildListing = $newbuildListing;
	}

	/**
	 * @param array $property
	 * @return integer
	 */
	protected function getEnergyCertificationRating(array $property){
	    if($property['ec_pending']){
	        return 10;//tramite
	    }
	    return !empty($property['ec']) ? $property['ec'] : 0;
	}

}
