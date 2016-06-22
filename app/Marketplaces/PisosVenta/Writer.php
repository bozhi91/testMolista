<?php namespace App\Marketplaces\PisosVenta;

class Writer extends \App\XML\Writer {

    protected $ended = false;

    public function __construct()
    {
        $this->openMemory();
        $this->startDocument('1.0', 'UTF-8');
    }

    public function end()
    {
        $this->endDocument();

        $this->ended = true;
    }

    public function addItem($item)
    {
        $this->write('Publicacion', ['Promociones' => ['Promocion' => $item] ]);
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
