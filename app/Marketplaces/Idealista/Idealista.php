<?php namespace App\Marketplaces\Idealista;

use App\Marketplaces\Base;
use App\Marketplaces\Interfaces\PublishPropertyXmlInterface;

class Idealista extends Base implements PublishPropertyXmlInterface {

    protected $iso_lang = 'es';

    protected $aggregator;
    protected $code;
    protected $reference;

    //Original properties data
    protected $properties = [];
    //Data client
    protected $client = [];
    //Formated secondhand data
    protected $secondhandListing = [];
    //Formated newbuild data
    protected $newbuildListing = [];

    public function __construct(array $config = [])
    {
        parent::__construct($config);

        //Set config params
        $this->setAggregator($config['aggregator']);
        $this->setCode(@$config['code']);
        $this->setReference(@$config['reference']);

        //Generate client node
        $this->setClient([
            'aggregator' => $this->getAggregator(),
            'code' => $this->getCode(),
            'reference' => $this->getReference()
        ]);
    }

    public function validateProperty(array $property)
    {
        return true;
    }

    //Process all data and generate XML
    public function getPropertiesXML(array $properties){

        $this->load($properties);
        return $this->generateXML();
    }

    //Load and process all data
    public function load(array $properties){

        //Set properties param
        $this->setProperties($properties);

        //Process properties array
        $this->processProperties();
    }

    //Generate XML string
    protected function generateXML(){

        //Start xml doc
        $writer = new \Sabre\Xml\Writer();
        $writer->openMemory();
        $writer->setIndent(true);
        $writer->startDocument('1.0', 'UTF-8');
        /*<?xml version="1.0" encoding="UTF-8"?>*/


        //Clients start
        $writer->startElement('clients');
        $writer->startElement('client');

        //Data client
        $writer->write( $this->getClient() );

        //Write secondhandListing
        $writer->startElement('secondhandListing');
        foreach($this->getSecondhandListing() as $k => $data){
            $writer->startElement('property');
            $this->writeXmlProperty($data, $writer);
            $writer->endElement();
        }
        $writer->endElement();

        //Write newbuildListing
        $writer->startElement('newbuildListing');
        foreach($this->getNewbuildListing() as $k => $data){
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

        //Clients end
        $writer->endElement();
        $writer->endElement();


        $request = $writer->outputMemory();
        return $request;

    }

    //Write XML property
    protected function writeXmlProperty($data, &$writer){
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

    protected function processProperties(){
        $properties = $this->getProperties();
        if(empty($properties)) die("Properties param is empty");

        foreach($properties as $k => $property){
            $this->addProperty($property);
        }

    }


    //Add property to secondhandListing or newbuildListing props
    protected function addProperty(array $property){

        $propertyIdealista = [
            'code' => $property['id'],
            'reference' => $property['reference'],
            'scope' => 1, //Idealista & Microsite
            'address' => $this->processAddress($property),
            'features' => $this->processFeatures($property),
            'operation_type' => $this->processOperation($property),
            'descriptions' => $this->processDescriptions($property),
            'links' => $this->processLinks($property),
            'images' => $this->processImages($property),
            'mode' => $property['mode'],
            'type' => $this->translateType($property['type'])
        ];


        if(empty($property['newly_build'])){
            $this->secondhandListing[] = $propertyIdealista;
        } else {
            $propertyIdealista['promo_code'] = $property['site_id'].'_'.$property['id'];
            $this->newbuildListing[] = $propertyIdealista;
        }
    }


    //Property type translate format
    protected function translateType($type){

        switch($type){
            case 'apartment':
            case 'duplex':
            case 'lot':
            case 'penthouse':
            default:
                $idealista_type = 'flat';
            break;
            case 'house':
            case 'Villa':
                $idealista_type = 'house';
            break;
            //case 'store':
        }

        return $idealista_type;
    }

    //Build images format
    protected function processImages($property){

        $images = [];
        foreach($property['images'] as $k => $url){
            $images[]['image'] = [
                'code' => 0,
                'url' => $url
            ];
        }

        return $images;

    }

    //Build descriptions format
    protected function processDescriptions($property){

        $descriptions = [];
        foreach($property['description'] as $locale => $description){
            $descriptions[]['description'] = [
                'language' => $this->translateLanguage($locale),
                'title' => substr($description, 0, 139),
                'comment' => substr($description, 0, 2499),
            ];
        }

        return $descriptions;
    }

    //Build links format
    protected function processLinks($property){

        $links = [];
        foreach($property['url'] as $locale => $link){
            $links[]['link'] = [
                'language' => $this->translateLanguage($locale),
                'comment' => '',
                'url' => $link,
            ];
        }

        return $links;
    }

    //Translate lang codes
    protected function translateLanguage($locale){
        $code = 1;
        switch($locale){
            case 'es':
                $code = 1;
            break;
            case 'en':
                $code = 2;
            break;
            case 'fr':
                $code = 3;
            break;
            case 'de':
                $code = 4;
            break;
            case 'pt':
                $code = 5;
            break;
            case 'it':
                $code = 6;
            break;
            case 'ca':
                $code = 7;
            break;
            case 'ru':
                $code = 8;
            break;
        }

        return $code;
    }

    //Build operation format
    protected function processOperation(array $property){

        $operationType = [
            'price' => $property['price'],
        ];

        return $operationType;
    }

    //Build features format
    protected function processFeatures(array $property){

        $oFeatures = $property['features'];

        $features = [
            'rooms' => $property['rooms'],
            'bathrooms' => $property['baths'],
            //'energyCertification' => $property['ec'],
            'constructedArea' => $property['size'],
            'constructedArea' => $property['size'],
            'conditionedAir' => empty($oFeatures['air-conditioning']) ? 1 : 2,
            'alarm' => (empty($oFeatures['alarm'])) ? 'false' : 'true',
            'balconyNumber' => empty($oFeatures['balcony']) ? 'false' : 'true',
            'elevator' => empty($oFeatures['elevator']) ? 'false' : 'true',
            'furniture' => (empty($oFeatures['furnished'])) ? 1 : 3,
            'garden' => empty($oFeatures['garden']) ? 'false' : 'true',
            'parkingSpacesInPrice' => (empty($oFeatures['parking']) && empty($oFeatures['garage'])) ? 'false' : 'true',
            'pool' => empty($oFeatures['pool']) ? 'false' : 'true',
            'lastFloor' => empty($oFeatures['atic']) ? 'false' : 'true',
            'windowsLocation' => (empty($oFeatures['exterior'])) ? 2 : 1
        ];

        //Special fields if don't have don't appear
        if(!empty($oFeatures['terrase'])){
            $features['terraceType'] = 0;
        }
        if(!empty($oFeatures['heating'])){
            $features['heating'] = 0;
        }


        return $features;
    }

    //Build address format
    protected function processAddress(array $property){

        $location = $property['location'];

        $address = [
            'visibility' => ($location['show_address']),
            'country' => $location['country'],
            'streetName' => $location['address'],
            'streetNumber' => '',
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
    public function getAggregator()
    {
        return $this->aggregator;
    }

    /**
     * @param mixed $aggregator
     */
    public function setAggregator($aggregator)
    {
        $this->aggregator = $aggregator;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @param mixed $reference
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @param array $properties
     */
    public function setProperties($properties)
    {
        $this->properties = $properties;
    }

    /**
     * @return array
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param array $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }

    /**
     * @return array
     */
    public function getSecondhandListing()
    {
        return $this->secondhandListing;
    }

    /**
     * @param array $secondhandListing
     */
    public function setSecondhandListing($secondhandListing)
    {
        $this->secondhandListing = $secondhandListing;
    }

    /**
     * @return array
     */
    public function getNewbuildListing()
    {
        return $this->newbuildListing;
    }

    /**
     * @param array $newbuildListing
     */
    public function setNewbuildListing($newbuildListing)
    {
        $this->newbuildListing = $newbuildListing;
    }



}
