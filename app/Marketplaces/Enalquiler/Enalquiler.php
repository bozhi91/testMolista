<?php namespace App\Marketplaces\Enalquiler;

use App\Marketplaces\Interfaces\PublishPropertyXmlInterface;

class Enalquiler implements PublishPropertyXmlInterface {

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

    public function getPropertiesXML(array $properties)
    {
        $this->writer = new Writer;

        foreach ($properties as $p)
        {
            $mapper = new Mapper($p, $this->iso_lang);
            if ($mapper->valid())
            {
                $this->writer->addItem([$mapper->map()]);
            }
        }

        return $this->writer->getXml();
    }

}
