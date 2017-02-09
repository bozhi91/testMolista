<?php

namespace App\Jobs;

use App\Property;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendRecommendProperty extends Job implements ShouldQueue {

	use InteractsWithQueue,
	 SerializesModels;

	protected $site;
	protected $property;
	protected $data;

	/**
	 * Create a new job instance.
	 *
	 * @return void
	 */
	public function __construct(Property $property, $data) {
		$this->property = $property;
		$this->data = $data;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle() {
		// Get property with translations
		$this->property = Property::withTranslations()
				->with('state')
				->with('city')
				->with('services')
				->with('images')
				->findOrFail($this->property->id);

		// Send email to user
		try {
			$this->sendUserEmail();
		} catch (\Exception $e) {
			\Log::error('ERROR: SendRecommendProperty -> sendUserEmail (' . $e->getMessage() . ')');
			\Log::error($e);
		}

		return true;
	}

	protected function sendUserEmail() {	
		// Backup current locale
		$locale_backup = \LaravelLocalization::getCurrentLocale();

		\LaravelLocalization::setLocale($this->data['locale']);

		$content = view('emails/property.recommend-friend', [
			'name' => $this->data['name'],
			'email' => $this->data['email'],
			'message' => $this->data['message'],
			'url' => $this->data['link'],
			'property' => $this->property,
			'site' => $this->property->site,
		])->render();

		$css_path = base_path('resources/assets/css/emails/moreinfo-customer.css');
		if ( file_exists($css_path) )
		{
			$emogrifier = new \Pelago\Emogrifier($content, file_get_contents($css_path));
			$content = $emogrifier->emogrify();
		}
		
		$to = $this->data['r_email'];
		$subject = \Lang::get('web/properties.recommendfriend.email.title', [ 'name'=> $this->data['name'] ]);
		
		$sent = $this->property->site->sendEmail([
			'to' => $to,
			'subject' => $subject,
			'content' => $content,
			'attachments' => [
				$this->property->getPdfFile($this->data['locale']) => [ 'as'=>"{$this->property->slug}.pdf" ],
			]
		]);
				
		// Restore backup locale
		\LaravelLocalization::setLocale($locale_backup);

		if ( $sent )
		{
			return true;
		}

		\Log::warning("SendMoreInfoProperty: error sending email to customer with email {$to}");
		return false;
	}
}
