<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Marketplaces\API;

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
	 * Create a new job instance.
	 *
	 * @param API $handler
	 * @param array $property
	 * @return void
	 */
	public function __construct(API $handler, array $property) {
		$this->handler = $handler;
		$this->property = $property;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle() {		
		return $this->handler->publishProperty($this->property);
	}

}
