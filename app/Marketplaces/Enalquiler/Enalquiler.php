<?php namespace App\Marketplaces\Enalquiler;

class Enalquiler extends \App\Marketplaces\XML {

    protected $iso_lang = 'es';

    public function validateProperty(array $property)
    {
        $mapper = new Mapper($property, $this->iso_lang);
        if ($mapper->valid())
        {
            return true;
        }

        return $mapper->errors();
    }

}
