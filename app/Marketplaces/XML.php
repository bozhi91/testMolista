<?php namespace App\Marketplaces;

use App\Marketplaces\Interfaces\PublishPropertyXmlInterface;

abstract class XML implements PublishPropertyXmlInterface {

    protected $writer;

    protected $iso_lang;

    public function __construct()
    {
        if (empty($this->iso_lang))
        {
            throw new \LogicException(static::class." must declare the attribute $iso_lang.");
        }
    }

    public function getPropertiesXML(array $properties)
    {
        $this->writer = static::getWriter();

        foreach ($properties as $p)
        {
            $mapper = static::getMapper($p, $this->iso_lang);
            if ($mapper->valid() === true)
            {
                $this->writer->addItem([$mapper->map()]);
            }
        }

        return $this->writer->getXml();
    }

    protected static function getWriter()
    {
        $class = static::getClassName().'\Writer';
        return new $class;
    }

    protected static function getMapper(array $property, $lang)
    {
        $class = static::getClassName().'\Mapper';
        return new $class($property, $lang);
    }

    private static function getClassName()
    {
        $parts = explode('\\', static::class);
        array_pop($parts);
        return implode('\\', $parts);
    }

}
