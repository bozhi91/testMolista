<?php

namespace App\Console\Commands;

use App\Site;
use Illuminate\Console\Command;
use App\Models\Site\ApiPublication;

class PublicarPropiedadesApi extends Command {

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'marketplace:api:publish {site? : Site id} {marketplaces? : Marketplaces codes}';

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

		// Properties to publish
		$properties = $helper->getMarketplaceProperties();
		$handler = $helper->getMarketplaceAdm();
		foreach($properties as $property){

			// Check if property has been changed since published
			$log = ApiPublication::where('site_id', $site->id)
					->where('marketplace_id', $marketplace->id)
					->where('property_id', $property['id'])
					->where('created_at', '>', $property['updated_at'])
					->where('action', 'publish')
					->first();

			if ($log) {
				continue;
			}

			$job = (new \App\Jobs\PublishPropertyApi($handler
					, $property
					, $site
					, $marketplace))->onQueue('publish');

			dispatch($job);
		}

		// Properties to delete
		$unpublish = $helper->getMarketplacePropertiesToUnpublish();

		$handler = $helper->getMarketplaceAdm();
		foreach($unpublish as $property){

			// If property has not been updated since deleted, do nothing
			$log = ApiPublication::where('site_id', $site->id)
					->where('marketplace_id', $marketplace->id)
					->where('property_id', $property['id'])
					->where('created_at', '>', $property['updated_at'])
					->where('action', 'delete')
					->first();

			if ($log) {
				continue;
			}

			$job = (new \App\Jobs\UnpublishPropertyApi($handler
					, $property
					, $site
					, $marketplace))->onQueue('publish');

			dispatch($job);
		}
	}

}
