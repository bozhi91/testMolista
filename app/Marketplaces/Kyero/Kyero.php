<?php namespace App\Marketplaces\Kyero;

// http://help.kyero.com/article/354-xml-import-specification

class Kyero extends V3\Kyero {

    protected $iso_lang = 'es';

    protected static function getClassName()
    {
        return 'App\Marketplaces\Kyero\\'.static::$version;
    }

}
