<?php namespace App\Marketplaces;

abstract class Writer extends \App\XML\Writer {

    protected $config = [];

    abstract public function start();

    public function setConfig(array $config)
    {
        $this->config = $config;
    }

}
