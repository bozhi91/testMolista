<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendSiteContactEmail extends Job implements ShouldQueue {

	use InteractsWithQueue, SerializesModels;

	/**
	 * @var Emai to
	 */
	private $email_to;

	/**
	 * @var Data
	 */
	private $data;

	/**
	 * Create a new job instance.
	 *
	 * @return void
	 */
	public function __construct($email_to, $data) {
		$this->email_to = $email_to;
		$this->data = $data;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		$email_to = $this->email_to;
		$data = $this->data;

		$subject = trans('corporate/signup.email.subject');
		$html = email_render_corporate('emails.site.contact', $data);

		\Mail::send('dummy', [ 'content' => $html ], function($message) use ($email_to, $data) {
			$message->from(env('MAIL_FROM_EMAIL'), env('MAIL_FROM_NAME'));
			$message->subject($data['subject']);
			$message->to($email_to);
		});
	}

}
