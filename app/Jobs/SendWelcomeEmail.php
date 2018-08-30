<?php namespace App\Jobs;

use App\Site;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendWelcomeEmail extends Job implements ShouldQueue
{
	use InteractsWithQueue, SerializesModels;

	protected $site;

	protected $data;

	public function __construct(Site $site, $locale)
	{
		// Set site
		$this->site = $site;

		// Set locale
		\App::setLocale($locale);
	}

	public function handle()
	{
		$data = $this->site->getSignupInfo(\App::getLocale());
		if ( !$data )
		{
			\Log::error("SendWelcomeEmail: error accessing getSignupInfo for site ID {$this->site->id}");
			return false;
		}

		// Transfer payment warning
		if ( @$data['pending_request']->payment_method == 'transfer' )
		{
			$site = $this->site;
			$subject = trans('corporate/signup.email.admin.subject');
			$html = view('emails.admin.inform-transfer-payment', $site)->render();
			$to = env('EMAIL_PAYMENT_WARNINGS_TO', 'admin@Contromia.com');
			\Mail::send('dummy', [ 'content' => $html ], function($message) use ($subject, $to, $data) {
				$message->from( env('MAIL_FROM_EMAIL'), env('MAIL_FROM_NAME') );
				$message->subject($subject);
				$message->to( $to );
			});
		}

		$subject = trans('corporate/signup.email.subject');
		$html = view('emails.corporate.signup', $data)->render();

		$css_path = base_path('resources/assets/css/emails/signup.css');
		if ( file_exists($css_path) )
		{
			$emogrifier = new \Pelago\Emogrifier($html, file_get_contents($css_path));
			$html = $emogrifier->emogrify();
		}

		\Mail::send('dummy', [ 'content' => $html ], function($message) use ($subject, $data) {
			$message->from( env('MAIL_FROM_EMAIL'), env('MAIL_FROM_NAME') );
			$message->subject($subject);
			$message->to( $data['owner_email'] );
			$message->bcc('luis@Contromia.com', 'Luis Krug');
		});
	}

}
