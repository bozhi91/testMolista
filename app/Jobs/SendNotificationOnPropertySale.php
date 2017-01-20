<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Site\Customer;
use App\Property;

class SendNotificationOnPropertySale extends Job implements ShouldQueue {

	use InteractsWithQueue,
	 SerializesModels;

	/**
	 * @var Customer
	 */
	private $customer;

	/**
	 * @var Property
	 */
	private $property;

	/**
	 * Create a new job instance.
	 *
	 * @param Customer $customer
	 * @param Property $property
	 * @return void
	 */
	public function __construct($customer, $property) {
		$this->customer = $customer;
		$this->property = $property;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle() {
		$locale = $this->customer->locale;

		$subject = trans('account/properties.email.sold.subject', [
			'title' => $this->property->translateOrDefault($locale)->title,
			'reference' => $this->property->ref
		]);

		$to = $this->customer->email;

		\Mail::send('emails.property.notify-close-transaction', [
			'title' => $subject, 'customer' => $this->customer
				], function($message) use ($subject, $to) {

			$message->from(env('MAIL_FROM_EMAIL'), env('MAIL_FROM_NAME'));
			$message->subject($subject);
			$message->to($to);
		});
	}

}
