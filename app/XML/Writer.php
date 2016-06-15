<?php namespace App\XML;

class Writer extends \XMLWriter {

    public function write($name, $content)
    {
        if (!is_array($content))
        {
            $this->startElement($name);
            $this->writeCData($content);
            $this->endElement();
        }
        else
        {
            $keys = array_keys($content);
            if (is_numeric(reset($keys)))
            {
                foreach ($content as $subname => $subcontent)
                {
                    $this->write($name, $subcontent);
                }
            }
            else
            {
                $this->startElement($name);
                foreach ($content as $subname => $subcontent)
                {
                    $this->write($subname, $subcontent);
                }
                $this->endElement();
            }
        }

        return true;
    }

    public function getXml()
    {
        return $this->flush(true);
    }

}
