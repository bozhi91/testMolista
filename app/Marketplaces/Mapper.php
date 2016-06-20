<?php namespace App\Marketplaces;

abstract class Mapper {

    protected $item;
    protected $iso_lang;

    protected $errors = [];

    public function __construct(array $item, $iso_lang)
    {
        $this->item = $item;
        $this->iso_lang = $iso_lang;
    }

    abstract public function map();

    abstract public function valid();

    protected function decimal($value, $precision = 2)
    {
        return number_format($value, $precision, '.', '');
    }

    protected function translate($item, $lang = null)
    {
        if (!is_array($item))
        {
            return false;
        }

        if (!$lang)
        {
            $lang = $this->iso_lang;
        }

        // return current lang if set...
        if (isset($item[$lang]))
        {
            return $item[$lang];
        }

        // ...return first available if not
        return reset($item);
    }

    public function errors()
    {
        return $this->errors;
    }

    protected function isSale()
    {
        return $this->item['mode'] == 'sale';
    }

    protected function isRent()
    {
        return $this->item['mode'] == 'rent';
    }

}
