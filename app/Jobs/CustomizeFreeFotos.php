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
use Illuminate\Support\Facades\Log;

class CustomizeFreeFotos extends Job implements ShouldQueue {

	use InteractsWithQueue;

	private $property;
	private $site;

	/**
	 * Create a new job instance.
	 * @param API $handler
	 * @param array $property
	 * @param integer $site
	 * @param integer $marketplace
	 * @return void
	 */
	public function __construct($site=null,$property=null) {
        $this->site     = $site;
        $this->property = $property;
	}

	public function handle() {

	    if( $this->property==null){
	        //this is executed when we enter the admin panel of a site. The handle function is called
            //from SiteSetup.php(App/Middleware/SiteSetup.php). This is applied for the Free sites only.
            Log::Info("===============================================================");
            Log::Info("Queued job executed. Watermarks applied to all images of the site:");
            //Log::Info("Site: ".json_encode($this->site));
            Log::Info("===============================================================");
           // \App\Http\Controllers\Account\PropertiesController::modifyImagesFreePlan();
        }
	    else{
	        //This piece is executed when a property is created or updated. In that case we're
            //updateing all the images for that property in that site.
            Log::Info("===============================================================");
	        Log::Info("Queued job executed. Watermarks applied to site's images.");
	        Log::Info("Property: ".json_encode($this->property));
            //Log::Info("Site: ".json_encode($this->site));
	        Log::Info("===============================================================");

           /* \App\Http\Controllers\Account\PropertiesController::customizePropertyImage(
                $this->site,
                $this->property);*/
        }
	}
}
