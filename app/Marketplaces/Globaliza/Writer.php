<?php namespace App\Marketplaces\Globaliza;

class Writer extends \App\XML\Writer {

    protected $ended = false;

    public function __construct()
    {
        $this->openMemory();
        $this->setIndent(true);
        $this->startDocument('1.0', 'UTF-8');
        $this->startElement('inmuebles');
        $this->writeAttributeNS('xmlns', 'xsi', null, 'http://www.w3.org/2001/XMLSchema-instance');
        $this->writeAttributeNS('xsi', 'noNamespaceSchemaLocation', null, './inmuebles.xsd');
    }

    public function end()
    {
        $this->endElement();
        $this->endDocument();

        $this->ended = true;
    }

    public function addItem($item)
    {
        $this->write('inmueble', $item);
    }

    public function getXml()
    {
        if (!$this->ended)
        {
            $this->end();
        }

        return parent::getXml();
    }

}
