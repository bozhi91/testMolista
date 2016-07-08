<?php namespace App\Marketplaces\GreenAcres;

class Writer extends \App\XML\Writer {

    protected $ended = false;

    public function __construct()
    {
        $this->openMemory();
        $this->setIndent(true);
        $this->startDocument('1.0', 'UTF-8');
        $this->startElement('Envelope');
        $this->startElement('Body');
        $this->startElement('add_adverts');
    }

    public function end()
    {
        $this->endDocument();

        $this->ended = true;
    }

    public function addItem($item)
    {
        $this->write('advert', $item);
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
