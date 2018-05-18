<?php namespace App\Marketplaces;

use App\Marketplaces\Interfaces\PublishPropertyXmlInterface;

abstract class XML extends Base implements PublishPropertyXmlInterface {

    protected $writer;

    public function getPropertiesXML(array $properties)
    {
        $this->writer = static::getWriter($this->config);

        if (method_exists($this->writer, 'start'))
        {
            $this->writer->start();
        }
        //echo json_encode($properties);

        foreach ($properties as $p)
        {
            $mapper = static::getMapper($p, $this->iso_lang, $this->config);

            //echo var_dump($mapper);
           // die;
            if ($mapper->valid())
            {
                $this->writer->addItem([$mapper->map()]);
            }
        }

        echo var_dump($this->writer->getXml()); die;

        return $this->writer->getXml();
    }

    protected static function getWriter($config)
    {
        $class = static::getClassName().'\Writer';
        $instance = new $class;

        if (method_exists($instance, 'setConfig'))
        {
            $instance->setConfig($config);
        }

        return $instance;
    }

}
