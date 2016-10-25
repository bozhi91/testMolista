<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendNotificationOnPropertySale extends Job implements ShouldQueue {

	use InteractsWithQueue,
	 SerializesModels;

	/**
	 * Create a new job instance.
	 *
	 * @return void
	 */
	public function __construct(array $emails, $locale) {

	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle() {
		
	}

}
