<?php namespace App\Marketplaces\Enalquiler\Owner;

class Writer extends \App\XML\Writer {

    protected $ended = false;

    public function __construct()
    {
        $this->openMemory();
        $this->startDocument('1.0', 'UTF-8');
        $this->startElementNS(null, 'propietarios', 'http://www.enalquiler.com/feeds/public');
        $this->writeAttributeNS('xmlns', 'xsi', null, 'http://www.w3.org/2001/XMLSchema-instance');
        $this->writeAttributeNS('xsi', 'schemaLocation', null, 'http://www.enalquiler.com/feeds/public http://www.enalquiler.com/feeds/public/usuarios.xsd');
    }

    public function end()
    {
        $this->endElement();
        $this->endDocument();

        $this->ended = true;
    }

    public function addItem($item)
    {
        $this->write('propietario', $item);
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
