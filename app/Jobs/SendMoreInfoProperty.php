<?php

namespace App\Jobs;

use App\Property;
use App\Models\Site\Customer;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMoreInfoProperty extends Job implements ShouldQueue
{
	use InteractsWithQueue, SerializesModels;

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
		$property = Property::withTranslations()
						->with('state')
						->with('city')
						->with('services')
						->with('images')
						->findOrFail($this->property->id);

		// Send email to property managers (site owners if no managers)
		$contacts = $property->contacts;
		if ( count($contacts) < 1 )
		{
			\Log::error("SendMoreInfoProperty: no contacts for property ID {$property->id}");
			return false;
		}

		$site = \App\Site::withTranslations()->findOrFail($property->site_id);

		$locale_backup = \LaravelLocalization::getCurrentLocale();

		foreach ($contacts as $contact)
		{
			\LaravelLocalization::setLocale($contact->locale ? $contact->locale : fallback_lang());

			$params = [
				'to' => $contact->email,
				'subject' => trans('web/properties.moreinfo.email.manager.title', [ 'ref'=>$property->ref ]),
				'content' => view('emails/property.moreinfo-manager', [
					'property' => $property,
					'customer' => $this->customer,
					'data' => $this->data,
				])->render()
			];
			if ( ! $site->sendEmail($params) )
			{
				\Log::warning("SendMoreInfoProperty: error sending email to manager/owner with email {$contact->email}");
			}
		}

		// Send email to customer (include PDF)
		\LaravelLocalization::setLocale($this->customer->locale);
		$content = view('emails/property.moreinfo-customer', [
			'site' => $site,
			'customer' => $this->customer,
			'property' => $property,
		])->render();
		if ( file_exists( base_path('resources/assets/css/emails/moreinfo-customer.css') ) )
		{
			$emogrifier = new \Pelago\Emogrifier($content, file_get_contents(base_path('resources/assets/css/emails/moreinfo-customer.css')));
			$content = $emogrifier->emogrify();
		}
		$sent = $site->sendEmail([
			'to' => $this->customer->email,
			'subject' => trans('web/properties.moreinfo.email.customer.title', [ 'title'=>$property->title ]),
			'content' => $content,
			'attachments' => [
				$property->getPdfFile($this->customer->locale) => [ 'as'=>"{$property->slug}.pdf" ],
			]
		]);
		if ( ! $sent )
		{
			\Log::warning("SendMoreInfoProperty: error sending email to customer with email {$this->customer->email}");
		}

		\LaravelLocalization::setLocale($locale_backup);

		return true;
	}
}
