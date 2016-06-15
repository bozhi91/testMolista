<?php namespace App\Marketplaces\Enalquiler;

use App\Marketplaces\Interfaces\OwnersXmlInterface;

class Enalquiler extends \App\Marketplaces\XML implements OwnersXmlInterface {

    protected $iso_lang = 'es';

    protected $writer;

    public function validateProperty(array $property)
    {
        $mapper = new Mapper($property, $this->iso_lang);
        if ($mapper->valid())
        {
            return true;
        }

        return $mapper->errors();
    }

    public function getOwnersXml(array $owners)
    {
        $writer = new Owner\Writer;

        foreach ($owners as $o)
        {
            $mapper = new Owner\Mapper($o);
            if ($mapper->valid() === true)
            {
                $writer->addItem([$mapper->map()]);
            }
            else
            {
                dd($mapper->valid());
            }
        }

        return $writer->getXml();
    }

}
