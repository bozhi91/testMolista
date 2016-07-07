<?php namespace App\Marketplaces;

use App\Marketplaces\Interfaces\MarketplaceInterface;

abstract class Base implements MarketplaceInterface {

    protected $configuration = [];

    public function getMarketplaceConfiguration()
    {
        return $this->configuration;
    }

}
