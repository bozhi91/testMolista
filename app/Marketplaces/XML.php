<?php namespace App\Marketplaces;

use App\Marketplaces\Interfaces\PublishPropertyXmlInterface;

abstract class XML extends Base implements PublishPropertyXmlInterface {

    protected $writer;

    protected $iso_lang;
    protected $config;

    public function __construct(array $config = [])
    {
        if (empty($this->iso_lang))
        {
            throw new \LogicException(static::class." must declare the attribute $iso_lang.");
        }

        $this->config = $config;
    }

    public function getPropertiesXML(array $properties)
    {
        $this->writer = static::getWriter();

        foreach ($properties as $p)
        {
            $mapper = static::getMapper($p, $this->iso_lang, $this->config);
            if ($mapper->valid())
            {
                $this->writer->addItem([$mapper->map()]);
            }
        }

        return $this->writer->getXml();
    }

    public function validateProperty(array $property)
    {
        $mapper = static::getMapper($property, $this->iso_lang, $this->config);
        if ($mapper->valid())
        {
            return true;
        }

        return $mapper->errors();
    }

    protected static function getWriter()
    {
        $class = static::getClassName().'\Writer';
        return new $class;
    }

    protected static function getMapper(array $property, $lang, array $config = [])
    {
        $class = static::getClassName().'\Mapper';
        return new $class($property, $lang, $config);
    }

    protected static function getClassName()
    {
        $parts = explode('\\', static::class);
        array_pop($parts);
        return implode('\\', $parts);
    }

}
