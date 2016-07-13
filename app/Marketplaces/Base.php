<?php namespace App\Marketplaces;

use App\Marketplaces\Interfaces\MarketplaceInterface;

abstract class Base implements MarketplaceInterface {

    protected $configuration = [];

    protected $currency = 'EUR';

    public function getMarketplaceConfiguration()
    {
        return $this->configuration;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

}
