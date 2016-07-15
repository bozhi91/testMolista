<?php namespace App\Marketplaces;

use App\Marketplaces\Interfaces\MarketplaceInterface;

abstract class Base implements MarketplaceInterface {

    protected $configuration = [];

    protected $iso_lang;
    protected $config;

    public function __construct(array $config = [])
    {
        if (empty($this->iso_lang))
        {
            throw new \LogicException(static::class." must declare the attribute $iso_lang.");
        }

        $this->config = $config;
    }

    public function getMarketplaceConfiguration()
    {
        return $this->configuration;
    }

}
