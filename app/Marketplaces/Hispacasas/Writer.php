<?php namespace App\Marketplaces\Hispacasas;

class Writer extends \App\XML\Writer {

    protected $ended = false;

    public function __construct()
    {
        $this->openMemory();
        $this->setIndent(true);
        $this->setIndentString('');
        $this->startDocument('1.0', 'UTF-8', 'yes');
        $this->startElement('root');
        $this->write('kyero', ['#feed_version' => $this->version]);
    }

    public function end()
    {
        $this->endElement(); // root
        $this->endDocument();

        $this->ended = true;
    }

    public function addItem($item)
    {
        $this->write('property', $this->encode($item));
    }

    /**
     * http://help.kyero.com/article/354-xml-import-specification#5
     *
     * @param  mixed $text
     * @return mixed
     */
    public function encode($text)
    {
        if (is_array($text))
        {
            foreach ($text as $i => $t)
            {
                $text[$i] = $this->encode($t);
            }
        }
        else
        {
            $rules = [
                '&' => '&#38;',
                '<' => '&#60;',
                '>' => '&#62;',
                '\'' => '&#39;',
                '"' => '&#34;',
            ];

            $text = str_replace(array_keys($rules), array_values($rules), $text);
        }

        return $text;
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
