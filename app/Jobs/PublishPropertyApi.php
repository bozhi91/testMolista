<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Marketplaces\Service;

class PublishPropertyApi extends Job implements ShouldQueue {

	use InteractsWithQueue,
	 SerializesModels;

	/**
	 * @var Service 
	 */
	private $service;
	
	/**
	 *
	 * @var array
	 */
	private $property;
	
	/**
	 * Create a new job instance.
	 *
	 * @return void
	 */
	public function __construct(Service $service, array $property) {
		$this->service = $service;
		$this->property = $property;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle() {
		$test = $this->service->publishProperty($this->property);
		
		dd($test);
	}

}
