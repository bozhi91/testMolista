<?php

namespace App\Marketplaces\Idealista;

class Writer extends \Sabre\Xml\Writer {
	
	public function write($value, $isRaw = false) {
		$isRaw ? parent::write($value) : parent::writeCdata($value);
	}
}
