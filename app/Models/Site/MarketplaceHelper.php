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
		// Define filepath
		$folder = $this->getXmlFolder();
		$filepath = $this->getXmlFilePath('properties');

		// Get XML content
		if (file_exists($filepath))
		{
			$content = file_get_contents($filepath);
		}
		// Or generate it
		else
		{
			if (!is_dir($folder))
			{
				\File::makeDirectory($folder, 0775, true, true);
			}

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

			$content = $this->marketplace_adm->getPropertiesXML($properties);

			\File::put($filepath, $content);
		}

		return $content;
	}

	public function getMarketplaceXmlOwners()
	{
		$config = @json_decode($this->marketplace->pivot->marketplace_configuration);
		if (empty($config->owner)) {
			return false;
		}

		// Define filepath
		$folder = $this->getXmlFolder();
		$filepath = $this->getXmlFilePath('owners');

		// Get XML content
		if (file_exists($filepath))
		{
			$content = file_get_contents($filepath);
		}
		// Or generate it
		else
		{
			if (!is_dir($folder))
			{
				\File::makeDirectory($folder, 0775, true, true);
			}

			$content = $this->marketplace_adm->getOwnersXml([
				[
					'id' => $this->site->id,
					'fullname' => @$config->owner->fullname,
					'email' => @$config->owner->email,
					'cif' => @$config->owner->cif,
				],
			]);

			\File::put($filepath, $content);
		}

		return $content;
	}

	public function getXmlFolder()
	{
		return "{$this->site->xml_path}/{$this->marketplace->code}";
	}

	public function getXmlFilePath($type)
	{
		return "{$this->getXmlFolder()}/{$type}.xml";
	}

	public function setMarketplace($marketplace)
	{

		$config = isset($marketplace->pivot->marketplace_configuration)
					? json_decode($marketplace->pivot->marketplace_configuration, true)
					: [];
		$config = isset($config['configuration']) ? $config['configuration'] : [];

		$this->marketplace = $marketplace;
		$this->marketplace_adm = new $marketplace->class_path($config);
	}

	public function setProperty($property)
	{
		$this->property_marketplace = $property->marketplace_info;

		// Add marketplace thumb folder to images
		if ( @$this->marketplace->configuration['thumb_flag'] )
		{
			switch ( $this->marketplace->configuration['thumb_flag'] )
			{
				case 'greenacres':
					$add_extension = '.jpg';
					break;
				default:
					$add_extension = '';
			}

			foreach ($this->property_marketplace['images'] as $key => $img)
			{
				$tmp = pathinfo($img);
				$this->property_marketplace['images'][$key] = implode('/', [
					$tmp['dirname'],
					$this->marketplace->configuration['thumb_flag'],
					$tmp['basename'] . $add_extension,
				]);
			}
		}
	}

	public function deleteXMLs()
	{
		return \File::deleteDirectory($this->site->xml_path, true);
	}

	public function getMarketplaceAdm()
	{
		return $this->marketplace_adm;
	}

}
