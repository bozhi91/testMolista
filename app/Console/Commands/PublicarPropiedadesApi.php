<?php

namespace App\Console\Commands;

use App\Site;
use Illuminate\Console\Command;

class PublicarPropiedadesApi extends Command {

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'properties:publish {site? : Site id} {marketplaces? : Marketplaces codes}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Publish properties for API marketplaces';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle() {
		$sites = $this->getSites();
		foreach ($sites as $site) {
			$query = $site->marketplaces()->where('upload_type', 'api');
			
			$marketplaceCodes = $this->argument('marketplaces');
			if ($marketplaceCodes) {
				$exploded = explode(',', $marketplaceCodes);
				$query->whereIn('code', $exploded);
			}
			
			$marketplaces = $query->get();
			foreach ($marketplaces as $marketplace) {
				$this->handleSingle($site, $marketplace);
			}
		}
	}

	/**
	 * @return Site[]
	 */
	private function getSites() {
		$siteId = $this->argument('site');

		$sites = [];

		if ($siteId) {
			$site = Site::find($siteId);
			if (!$site) {
				$this->error("Site with id \"{$siteId}\" does not exist");
				return false;
			}
			$sites[] = $site;
		} else {
			$sites = Site::all();
		}

		return $sites;
	}

	/**
	 * @param Site $site
	 * @param Marketplace $marketplace
	 */
	private function handleSingle($site, $marketplace) {
		$helper = new \App\Models\Site\MarketplaceHelper($site);
		$helper->setMarketplace($marketplace);		
		
		$properties = $helper->getMarketplaceProperties();
		$handler = $helper->getMarketplaceAdm();
		foreach($properties as $property){
			$job = (new \App\Jobs\PublishPropertyApi($handler
					, $property
					, $site
					, $marketplace))->onQueue();
			
			$this->dispatch($job);
		}
	}

}
