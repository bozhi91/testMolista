<?php namespace App\Marketplaces;

use App\Marketplaces\Interfaces\MarketplaceInterface;

abstract class Base implements MarketplaceInterface {

    protected $configuration = [];

    protected $iso_lang;
    protected $config;

    protected $currency = 'EUR';

    public function __construct(array $config = [])
    {
        if (empty($this->iso_lang))
        {
            throw new \LogicException(static::class.' must declare the attribute $iso_lang.');
        }

        $this->config = $config;
    }

    public function getMarketplaceConfiguration()
    {
        return $this->configuration;
    }

    public function getCurrency()
    {
        return $this->currency;
    }
	
	public function validateProperty(array $property)
    {
        $mapper = static::getMapper($property, $this->iso_lang, $this->config);
        if ($mapper->valid())
        {
            return true;
        }

        return $mapper->errors();
    }
	
	protected static function getMapper(array $property, $lang, array $config = [])
    {
        $class = static::getClassName().'\Mapper';
        return new $class($property, $lang, $config);
    }

    protected static function getClassName()
    {
        $parts = explode('\\', static::class);
        array_pop($parts);
        return implode('\\', $parts);
    }

	public function getAttributes(){
		return [];
	}

    public function getFeedUrl()
    {
        return null;
    }

}
