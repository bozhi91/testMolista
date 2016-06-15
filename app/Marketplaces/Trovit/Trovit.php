<?php namespace App\Marketplaces\Trovit;

use App\Marketplaces\Interfaces\PublishPropertyXmlInterface;

class Trovit implements PublishPropertyXmlInterface {

    protected $iso_lang = 'es';
    protected $writer;

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
