<?php namespace App\Marketplaces\Trovit;

class Trovit extends \App\Marketplaces\XML {

    protected $iso_lang = 'es';

    public function validateProperty(array $property)
    {
        return true;
    }

}
