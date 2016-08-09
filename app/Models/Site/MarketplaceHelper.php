<?php namespace App\Models\Site;

class MarketplaceHelper
{

	protected $site;

	protected $marketplace;
	protected $marketplace_adm;
	protected $marketplace_currency;

	protected $property;
	protected $property_marketplace;

	protected $currencies_rates = [];

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

		if ( isset($data['marketplace_export_all']) )
		{
			$columns['marketplace_export_all'] = @intval($data['marketplace_export_all']);
		}

		if ( isset($data['marketplace_maxproperties']) )
		{
			$columns['marketplace_maxproperties'] = @intval($data['marketplace_maxproperties']);
			if ( !$columns['marketplace_maxproperties'] )
			{
				$columns['marketplace_maxproperties'] = null;
			}
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

		// Check if needs regeneration
		if ( file_exists($filepath) )
		{
			// Max 1 week old
			if ( filemtime($filepath) < time()-(60*60*24*7) )
			{
				@unlink($filepath);
			}
		}

		// Get XML content
		if ( file_exists($filepath) && env('APP_DEBUG', false) === false )
		{
			dd('production');
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

			$query = $this->site->properties();

			// Export all properties to marketplace
			if ( @$this->marketplace->pivot->marketplace_export_all )
			{
			}
			// Only enabled for this marketplace
			else
			{
				$query->ofMarketplace($this->marketplace->id);
			}

			$source = $query->withEverything()
							->orderBy('highlighted','desc')
							->orderBy('updated_at','desc')
							->get();

			// Site limitation
			$total_allowed = intval($this->marketplace->pivot->marketplace_maxproperties);
			// Plan limitation
			if ( $this->site->plan_property_limit > 0 )
			{
				// Only if no site limitation or bigger that site limitation
				if ( !$total_allowed || $total_allowed > $this->site->plan_property_limit )
				{
					$total_allowed = $this->site->plan_property_limit;
				}
			}

			$total_properties = $source->count();
			$check_limit = ( $total_allowed > 0 && $total_allowed < $total_properties );

			foreach ($source as $key => $property)
			{
				// Prepare property
				$this->setProperty($property);

				// If check_limit, validate property
				if ( $check_limit && !$this->marketplace_adm->validateProperty($this->property_marketplace) )
				{
					continue;
				}

				// Add property to feed
				$properties[] = $this->property_marketplace;

				// If check_limit and total allowed has been reached
				if ( $check_limit && count($properties) >= $total_allowed )
				{
					break;
				}
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

		// Marketplace without pivot data
		if ( !isset($marketplace->pivot->marketplace_configuration) )
		{
			// Create marketplace from site
			$marketplace = $this->site->marketplaces()->find($marketplace->id);
		}

		$config = isset($marketplace->pivot->marketplace_configuration)
					? json_decode($marketplace->pivot->marketplace_configuration, true)
					: [];
		$config = isset($config['configuration']) ? $config['configuration'] : [];

		$this->marketplace = $marketplace;

		$this->marketplace_adm = new $marketplace->class_path($config);
		$this->marketplace_currency = $this->marketplace_adm->getCurrency();
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

		// Fix price
		if ( $this->property_marketplace['currency'] != $this->marketplace_currency )
		{
			$currency = $this->property_marketplace['currency'];
			if ( !array_key_exists($currency, $this->currencies_rates) )
			{
				$this->currencies_rates[$currency] = \App\Models\CurrencyConverter::convert(1, $currency, $this->marketplace_currency);
			}

			$this->property_marketplace['price'] = $this->property_marketplace['price'] * $this->currencies_rates[$currency];
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
