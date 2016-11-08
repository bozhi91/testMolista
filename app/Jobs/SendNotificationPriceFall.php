<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Property;
use App\Models\Site\Customer;
use App\User;

class SendNotificationPriceFall extends Job implements ShouldQueue {

	use InteractsWithQueue,
	 SerializesModels;

	/**
	 * @var User 
	 */
	private $_agent;
	
	/**
	 * @var Customer
	 */
	private $_customer;
	
	/**
	 * @var Property
	 */
	private $_property;
	
	/**
	 * Create a new job instance.
	 *
	 * @param Property $property
	 * @param User $user
	 * @param Customer $customer
	 * @return void
	 */
	public function __construct($property, $user = null, $customer = null) {
		$this->_property = $property;
		$this->_agent = $user;
		$this->_customer = $customer;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle() {	
		$subject = trans('account/properties.email.price.fallen.subject', [
			'reference' => $this->_property->ref
		]);
			
		if($this->_agent){
			$name = $this->_agent->name;
			$to = $this->_agent->email;
			$locale = $this->_agent->locale;
		} else {
			$name = $this->_customer->fullName;
			$to = $this->_customer->email;
			$locale = $this->_customer->locale;
		}
					
		\Mail::send('emails.property.notification-price-fall', [
			'name' => $name,
			'reference' => $this->_property->ref,
			'title' => $this->_property->translateOrDefault($locale)->title,
			'current' => $this->_property->price,
			'url' => $this->_property->fullUrl,
		], function($message) use ($subject, $to) {
			$message->from(env('MAIL_FROM_EMAIL'), env('MAIL_FROM_NAME'));
			$message->subject($subject);
			$message->to($to);
		});
	}

}
