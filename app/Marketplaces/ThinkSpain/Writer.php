<?php namespace App\Marketplaces\ThinkSpain;

class Writer extends \App\Marketplaces\Writer {

    protected $ended = false;

    public function __construct()
    {
        $this->openMemory();
        $this->startDocument('1.0', 'UTF-8', 'yes');
        $this->startElement('root');
    }

    public function start()
    {
        $this->write('thinkspain', ['#import_version' => 1.3]);
        $this->write('agent', ['name' => @$this->config['agent_name']]);
    }

    public function end()
    {
        $this->endElement();
        $this->endDocument();

        $this->ended = true;
    }

    public function addItem($item)
    {
        $this->write('property', $item);
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
