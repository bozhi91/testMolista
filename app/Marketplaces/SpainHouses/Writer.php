<?php namespace App\Marketplaces\SpainHouses;

class Writer extends \App\XML\Writer {

    protected $ended = false;

    public function __construct()
    {
        $this->openMemory();
        $this->startDocument('1.0', 'UTF-8');
        $this->startElement('carteraPropiedades');
        $this->writeAttributeNS('xmlns', 'xsi', null, 'http://www.w3.org/2001/XMLSchema-instance');
        $this->writeAttributeNS('xsi', 'noNamespaceSchemaLocation', null, 'http://www.entersoftweb.com/xcp/sh/xsd/cartera.xsd');
        $this->writeElement('fecha', date('Y-m-d').'T'.date('H:i:s'));
        $this->startElement('listaPropiedades');
    }

    public function end()
    {
        $this->endElement();
        $this->endElement();
        $this->endDocument();

        $this->ended = true;
    }

    public function addItem($item)
    {
        $this->write('propiedad', $item);
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
