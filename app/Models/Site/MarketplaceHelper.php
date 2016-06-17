<?php namespace App\Models\Site;

class MarketplaceHelper
{

	protected $site;

	protected $marketplace;
	protected $marketplace_adm;

	protected $property;
	protected $property_marketplace;

	public function __construct($site)
	{
		$this->site = $site;
	}

	public function saveConfiguration($marketplace_id, $data)
	{
		$columns = [];

		if ( isset($data['marketplace_enabled']) )
		{
			$columns['marketplace_enabled'] = empty($data['marketplace_enabled']) ? 0 : 1;
		}

		if ( isset($data['marketplace_configuration']) )
		{
			if ( is_array($data['marketplace_configuration']) )
			{
				$columns['marketplace_configuration'] = json_encode($data['marketplace_configuration']);
			}
			else
			{
				$columns['marketplace_configuration'] = json_encode([]);
			}
		}

		if ( !empty($columns) )
		{
			$this->deleteXMLs();

			if ( $this->site->marketplaces->contains($marketplace_id) )
			{
				$this->site->marketplaces()->updateExistingPivot($marketplace_id, $columns);
			}
			else
			{
				$this->site->marketplaces()->attach($marketplace_id, $columns);
			}
		}

		return true;
	}

	public function savePropertyMarketplaces($property_id, $marketplaces_ids)
	{
		$property = $this->site->properties()->find($property_id);
		if ( !$property )
		{
			return false;
		}

		$this->deleteXMLs();

		if ( empty($marketplaces_ids) )
		{
			$property->marketplaces()->detach();
			return true;
		}

		if ( !is_array($marketplaces_ids) )
		{
			$marketplaces_ids = [ $marketplaces_ids ];
		}

		$property->marketplaces()->sync($marketplaces_ids);

		return true;
	}

	public function checkReadyProperty($marketplace,$property) 
	{
		$this->setMarketplace($marketplace);
		$this->setProperty($property);

		return $this->marketplace_adm->validateProperty($this->property_marketplace);
	}

	public function getMarketplaceXml($marketplace, $type=false)
	{
		$this->setMarketplace($marketplace);

		switch ($type)
		{
			case 'owners':
				return $this->getMarketplaceXmlOwners();
			case 'properties':
				return $this->getMarketplaceXmlProperties();
		}

		\Log::warning("XML of type {$type} is not defined");
		return false;
	}
	public function getMarketplaceXmlProperties()
	{
		// Get properties
		$properties = [];

		$source = $this->site->properties()
					->enabled()->withEverything()
					->ofMarketplace($this->marketplace->id)
					->get();
		foreach ($source as $key => $property) 
		{
			$this->setProperty($property);
			$properties[] = $this->property_marketplace;
		}

		return $this->marketplace_adm->getPropertiesXML($properties);
	}
	public function getMarketplaceXmlOwners()
	{
		$config = @json_decode( $this->marketplace->pivot->marketplace_configuration );

		if ( empty($config->owner) )
		{
			return false;
		}

		return $this->marketplace_adm->getOwnersXml([
			[
				'id' => $this->site->id,
				'fullname' => @$config->owner->fullname,
				'email' => @$config->owner->email,
				'cif' => @$config->owner->cif,
			],
		]);

		return $this->marketplace_adm->getOwnersXml($owners);
	}

	public function setMarketplace($marketplace) 
	{
		$this->marketplace = $marketplace;
		$this->marketplace_adm = new $marketplace->class_path();
	}

	public function setProperty($property) 
	{
		$this->property_marketplace = $property->marketplace_info;

		// Add marketplace thumb folder to images
		if ( @$this->marketplace->configuration['thumb_flag'] )
		{
			foreach ($this->property_marketplace['images'] as $key => $img) 
			{
				$tmp = pathinfo($img);
				$this->property_marketplace['images'][$key] = implode('/', [ 
					$tmp['dirname'],
					$this->marketplace->configuration['thumb_flag'],
					$tmp['basename'],
				]);
			}
		}
	}

	public function deleteXMLs() 
	{
		return \File::deleteDirectory($this->site->xml_path, true);
	}

}
