<?php namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;

use App\Http\Requests;

class FeedsController extends \App\Http\Controllers\WebController
{
	protected $marketplace;
	protected $configuration;
	protected $site_configuration;

	public function getProperties($code)
	{
		return $this->outputXml($code,'properties');
	}

	public function getOwners($code)
	{
		return $this->outputXml($code,'owners');
	}

	public function outputXml($code,$type)
	{
		$code = $this->setMarketplace($code);

		switch ($type)
		{
			case 'owners':
				if ( empty($this->configuration['xml_owners']) )
				{
					abort(404);
				}
				break;
		}

		// Define filepath
		$folder = "{$this->site->xml_path}/{$code}";
		$filepath = "{$folder}/{$type}.xml";

		// Get XML content
		if ( false && file_exists($filepath) )
		{
			$content = file_get_contents($filepath);
		}
		// Or generate it
		else
		{
			if ( !is_dir($folder) )
			{
				\File::makeDirectory($folder, 0775, true, true);
			}
			$content = $this->site->marketplace_helper->getMarketplaceXml($this->marketplace,$type);
			if ( !$content )
			{
				abort(404);
			}
			\File::put($filepath, $content);
		}

		// Output XML
		return response($content, '200')->withHeaders([
			'Content-Type' => 'text/xml'
		]);

	}

	public function setMarketplace($code)
	{
		$code = preg_replace('#\.xml$#', '', $code);

		$this->marketplace = $this->site->marketplaces()->enabled()
							->wherePivot('marketplace_enabled',1)
							->where('code', $code)
							->first();
		if ( !$this->marketplace )
		{
			abort(404);
		}

		$this->configuration = $this->marketplace->configuration;

		$this->site_configuration = @json_decode($this->marketplace->pivot->marketplace_configuration);

		return $code;
	}

}
