<?php namespace App\Jobs;

use App\Property;
use App\Models\Site\Customer;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMoreInfoProperty extends Job implements ShouldQueue
{
	use InteractsWithQueue, SerializesModels;

	protected $site;
	protected $property;
	protected $customer;
	protected $data;

	/**
	* Create a new job instance.
	*
	* @return void
	*/
	public function __construct(Property $property, Customer $customer, $data)
	{
		$this->customer = $customer;
		$this->property = $property;
		$this->data = $data;
	}

	/**
	* Execute the job.
	*
	* @return void
	*/
	public function handle()
	{
		// Get property with translations
		$this->property = Property::withTranslations()
						->with('state')
						->with('city')
						->with('services')
						->with('images')
						->findOrFail($this->property->id);

		// Create ticket
		try
		{
			$this->createTicket();
		}
		catch (\Exception $e)
		{
			\Log::error('ERROR: SendMoreInfoProperty -> createTicket ('.$e->getMessage().')');
			\Log::error($e);
		}

		// Send email to user
		try
		{
			$this->sendUserEmail();
		}
		catch (\Exception $e)
		{
			\Log::error('ERROR: SendMoreInfoProperty -> sendUserEmail ('.$e->getMessage().')');
			\Log::error($e);
		}

		return true;
	}


	protected function sendUserEmail()
	{
		// Backup current locale
		$locale_backup = \LaravelLocalization::getCurrentLocale();

		\LaravelLocalization::setLocale($this->data['locale']);

		$content = view('emails/property.moreinfo-customer', [
			'site' => $this->property->site,
			'customer' => $this->customer,
			'property' => $this->property,
		])->render();

		$css_path = base_path('resources/assets/css/emails/moreinfo-customer.css');
		if ( file_exists($css_path) )
		{
			$emogrifier = new \Pelago\Emogrifier($content, file_get_contents($css_path));
			$content = $emogrifier->emogrify();
		}

		$sent = $this->property->site->sendEmail([
			'to' => $this->customer->email,
			'subject' => trans('web/properties.moreinfo.email.customer.title', [ 'title'=>$this->property->title ]),
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

		\Log::warning("SendMoreInfoProperty: error sending email to customer with email {$this->customer->email}");
		return false;
	}


	protected function createTicket()
	{
		// Backup current locale
		$locale_backup = \LaravelLocalization::getCurrentLocale();

		if ( $manager = $this->property->unique_manager )
		{
			$user_id = $manager->ticket_user_id;
			\LaravelLocalization::setLocale($manager->locale ? $manager->locale : fallback_lang());
		}
		else
		{
			$user_id = null;
		}

		$body = view('emails.property.moreinfo-manager', [
			'email_content_only' => true,
			'property' => $this->property,
			'customer' => $this->customer,
			'data' => $this->data,
		])->render();

		$res = $this->property->site->ticket_adm->createTicket([
			'contact_id' => $this->customer->ticket_contact_id,
			'user_id' => $user_id,
			'item_id' => $this->property->ticket_item_id,
			'source' => 'web',
			'subject' => trans('web/properties.moreinfo.email.customer.title', [ 'title'=>$this->property->title ]),
			'body' => strip_tags($body),
		]);

		// Restore backup locale
		\LaravelLocalization::setLocale($locale_backup);

		return $res;
	}
	protected function sendManagerEmail()
	{
		// Send email to property managers (site owners if no managers)
		$contacts = $this->property->contacts;
		if ( count($contacts) < 1 )
		{
			\Log::error("SendMoreInfoProperty: no contacts for property ID {$property->id}");
			return false;
		}

		// Backup current locale
		$locale_backup = \LaravelLocalization::getCurrentLocale();

		foreach ($contacts as $contact)
		{
			\LaravelLocalization::setLocale($contact->locale ? $contact->locale : fallback_lang());

			$res = $this->property->site->sendEmail([
				'to' => $contact->email,
				'subject' => trans('web/properties.moreinfo.email.manager.title', [ 'ref'=>$this->property->ref ]),
				'content' => view('emails/property.moreinfo-manager', [
					'property' => $this->property,
					'customer' => $this->customer,
					'data' => $this->data,
				])->render()
			]);
				
			if ( !$res )
			{
				\Log::warning("SendMoreInfoProperty: error sending email to manager/owner with email {$contact->email}");
			}
		}

		// Restore backup locale
		\LaravelLocalization::setLocale($locale_backup);

		return true;
	}

}
