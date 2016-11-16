<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Marketplaces\API;
use App\Site;
use App\Models\Marketplace;
use App\Models\Site\ApiPublication;

class PublishPropertyApi extends Job implements ShouldQueue {

	use InteractsWithQueue,
	 SerializesModels;

	/**
	 * @var API
	 */
	private $handler;

	/**
	 *
	 * @var array
	 */
	private $property;

	/**
	 * @var Site
	 */
	private $site;

	/**
	 * @var Marketplace
	 */
	private $marketplace;

	/**
	 * Create a new job instance.
	 *
	 * @param API $handler
	 * @param array $property
	 * @param integer $site
	 * @param integer $marketplace
	 * @return void
	 */
	public function __construct(API $handler, array $property, $site, $marketplace) {
		$this->handler = $handler;
		$this->property = $property;
		$this->site = $site;
		$this->marketplace = $marketplace;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle() {
		$result = $this->handler->publishProperty($this->property);
		
		$log = new ApiPublication();
		$log->site_id = $this->site->id;
		$log->marketplace_id = $this->marketplace->id;
		$log->property_id = $this->property['id'];
		$log->property = $this->property;
		$log->result = $result;
		$log->save();
	}

}
