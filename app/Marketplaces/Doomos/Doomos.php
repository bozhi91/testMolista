<?php

namespace App\Marketplaces\Doomos;

use App\Marketplaces\Trovit\Trovit;
use App\Marketplaces\Interfaces\UnifiedXmlInterface;

class Doomos extends Trovit implements UnifiedXmlInterface {

	/**
	 * @param array $files
	 * @return string
	 */
	public function getUnifiedXml(array $files) {
		$dom = new \DOMDocument();
		$dom->appendChild($dom->createElement('trovit'));

		foreach ($files as $xml) {
			$addDom = new \DOMDocument();
			$addDom->loadXml($xml);
			if ($addDom->documentElement) {
				foreach ($addDom->documentElement->childNodes as $node) {
					$dom->documentElement->appendChild(
							$dom->importNode($node, TRUE)
					);
				}
			}
		}

		return $dom->saveXML();
	}

}
