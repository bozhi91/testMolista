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

class CustomizeFreeFotos extends Job implements ShouldQueue {

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
    protected $id;
	/**
	 * Create a new job instance.
	 *
	 * @param API $handler
	 * @param array $property
	 * @param integer $site
	 * @param integer $marketplace
	 * @return void
	 */
	public function __construct($site=null,$property=null) {
        $this->site = $site;
        $this->property = $property;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle() {

	    if( $this->site==null)  {
            \App\Http\Controllers\Account\PropertiesController::modifyImagesFreePlan();
        }
	    else {
            \App\Http\Controllers\Account\PropertiesController::customizePropertyImage(
                $this->property,
                $this->site);
        }
	}

}
