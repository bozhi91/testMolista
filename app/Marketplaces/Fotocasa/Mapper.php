<?php

namespace App\Marketplaces\Fotocasa;

class Mapper extends \App\Marketplaces\Inmofactory\Mapper {

	/**
	 * @return array
	 */
	protected function getPropertyPublications() {
		$publications = [];

		$publications[] = [
			"PublicationId" => 1,
			"PublicationTypeId" => 2,
		];

		return $publications;
	}

}
