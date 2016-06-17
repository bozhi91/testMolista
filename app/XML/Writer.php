<?php namespace App\XML;

class Writer extends \XMLWriter {

    /**
     * Writes an element and its content.
     * $name can contain attributes in this format: "Publicacion@attrbiute1=Attribute Value@attribute2=Attribute Value 2"
     *
     * @param  string $name
     * @param  string|array $content
     * @return boolean
     */
    public function write($name, $content)
    {
        $attributes = [];

        // Search $name for attributes
        $parts = explode('@', $name);
        if (count($parts) > 1)
        {
            foreach (array_slice($parts, 1) as $attr)
            {
                list($key, $value) = explode('=', $attr);
                $attributes[$key] = $value;
            }
        }

        // Final name
        $name = reset($parts);

        if (!is_array($content))
        {
            $this->startElement($name);
            foreach ($attributes as $key => $value)
            {
                $this->writeAttribute($key, $value);
            }
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
                foreach ($attributes as $key => $value)
                {
                    $this->writeAttribute($key, $value);
                }
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
