<?php namespace App\Marketplaces\Hispacasas;

// http://help.kyero.com/article/354-xml-import-specification

class Hispacasas extends V3\Hispacasas {

    protected $iso_lang = 'es';

    protected static function getClassName()
    {
        return 'App\Marketplaces\Hispacasas\\'.static::$version;
    }

}
