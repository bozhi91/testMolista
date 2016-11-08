<?php

namespace App\Marketplaces\Idealista;

class Writer extends \XMLWriter {

	/**
	 * Writes an element and its content.
	 * $name can contain attributes in this format: "Publicacion@attrbiute1=Attribute Value@attribute2=Attribute Value 2"
	 *
	 * @param  string|array $content
	 * @param  string $name
	 * @return boolean
	 */
	public function write($content, $name = '') {
		$attributes = [];

		// Search $name for attributes
		$parts = explode('@', $name);
		if (count($parts) > 1) {
			foreach (array_slice($parts, 1) as $attr) {
				list($key, $value) = explode('=', $attr);
				$attributes[$key] = $value;
			}
		}

		// Final name
		$name = reset($parts);

		// Is raw?
		$is_raw = strpos($name, '#') === 0;
		$org_name = $name;
		if ($is_raw) {
			$name = substr($name, 1);
		}
		
		if (!is_array($content)) {
			if(!empty($name)){
				$this->startElement($name);
			}
				
			foreach ($attributes as $key => $value) {
				$this->writeAttribute($key, $value);
			}
			$is_raw ? $this->text($content) : $this->writeCData($content);
			
			
			if(!empty($name)){
				$this->endElement();
			}
		} else {
			$keys = array_keys($content);		
			if (is_numeric(reset($keys))) {
				foreach ($content as $subname => $subcontent) {
					$this->write($subcontent, $org_name);
				}
			} else {	
				
				
				
				if(!empty($name)){
					$this->startElement($name);
				}
				
				foreach ($attributes as $key => $value) {
					$this->writeAttribute($key, $value);
				}
				foreach ($content as $subname => $subcontent) {
					$this->write($subcontent, $subname);
				}
				
				if(!empty($name)){
					$this->endElement();
				}
			}
		}

		return true;
	}

}
