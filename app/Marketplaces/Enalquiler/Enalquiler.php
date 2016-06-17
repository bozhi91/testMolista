<?php namespace App\Marketplaces\Enalquiler;

use App\Marketplaces\Interfaces\OwnersXmlInterface;

class Enalquiler extends \App\Marketplaces\XML implements OwnersXmlInterface {

    protected $iso_lang = 'es';

    protected $writer;

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
        }

        return $writer->getXml();
    }

}
