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

		$content = $this->site->marketplace_helper->getMarketplaceXml($this->marketplace,$type);

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

	public function unifiedFeed($code, $hash)
	{
		$marketplace = \App\Models\Marketplace::where('code', $code)
							->enabled()
							->first();

		if ( !$marketplace || $marketplace->integration_secret != $hash)
		{
			abort(404);
		}

		// Find client with the marketplace enabled
		$sites = \App\Site::enabled()
						->whereHas('marketplaces', function($query) use ($marketplace){
							$query->where('marketplace_enabled', 1);
							$query->where('marketplace_id', $marketplace->id);
						})
						->get();

		$files = [];
		foreach ($sites as $site) {
			$files[$site->id] = $site->marketplace_helper->getMarketplaceXml($marketplace, 'properties');
		}

		$adm = new $marketplace->class_path;
		$content = $adm->getUnifiedXml($files);

		// Output XML
		return response($content, '200')->withHeaders([
			'Content-Type' => 'text/xml'
		]);
	}

	public function yaencontre()
	{
		$user_code = $this->request->get('id');
		if (!$user_code) {
			abort(404);
		}

		// Check if marketplace is active
		$marketplace = \App\Models\Marketplace::where('id', env('YAENCONTRE_MARKETPLACE_ID'))->enabled()->first();
		if (!$marketplace) {
			abort(404);
		}

		// Find the site by code
		$site = \App\Site::enabled()->whereIn('id', function($query) use ($user_code, $marketplace) {
			$query->select('site_id')
					->from('sites_marketplaces')
					->where('marketplace_configuration', 'like', '%"oficina":"'.$user_code.'"%')
					->where('marketplace_enabled', 1)
					->where('marketplace_id', $marketplace->id);
		})->first();

		if (!$site) {
			abort(404);
		}

		// Set the site
		$this->site = $site;

		// Get the feed
		return $this->getProperties($marketplace->code);
	}

}
