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
	public function __construct() {

	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle() {
		$subject = trans('corporate/signup.email.subject');
		
	
		/*$css_path = base_path('resources/assets/css/emails/signup.css');
		if ( file_exists($css_path) )
		{
			$emogrifier = new \Pelago\Emogrifier($html, file_get_contents($css_path));
			$html = $emogrifier->emogrify();
		}*/
		
		\Mail::send('emails.property.notify-close-transaction', [ ], function($message) use ($subject) {
			$message->from( env('MAIL_FROM_EMAIL'), env('MAIL_FROM_NAME') );
			$message->subject($subject);
			$message->to('demmbox@gmail.com');
			//$message->bcc('luis@molista.com', 'Luis Krug');
		});
		
		
		
		
	}

}
