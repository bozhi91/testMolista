<?php namespace App\Models\Site;

class PlanHelper
{
	protected $site;

	protected $request;

	protected $plan;

	protected $data;

	protected $data_default = [
		'plan_id' => null,
		'payment_interval' => null,
		'payment_method' => null,
		'iban_account' => null,
		'stripe_id' => null,
		'paid_until' => null,
	];

	protected $info = [];

	public function __construct($site_id)
	{
		$this->setSite($site_id);
	}

	public function setSite($site_id)
	{
		$this->site = \App\Site::withTranslations()->findOrFail($site_id);
	}

	public function updatePlan($request)
	{
		$this->request = $request;

		$this->data = $this->data_default;

		if ( !$this->setPlanInfo() )
		{
			return false;
		}

		if ( !$this->setPaymentInfo() )
		{
			return false;
		}

		$this->setSiteOldInfo();

		if ( !$this->plan || $this->plan->is_free )
		{
			return $this->setPlanFree();
		}

		switch ( $this->data['payment_method'])
		{
			case 'stripe':
				return $this->setPlanStripe();
			case 'transfer':
				return $this->setPlanTransfer();
		}

		\Log::error("Pay method {$this->data['payment_method']} is not defined");
		return false;
	}

	public function setPlanFree()
	{
		// Update payment info
		$data = array_merge($this->data_default, [
			'plan_id' => $this->data['plan_id'],
			'payment_interval' => $this->data['payment_interval'],
		]);

		$this->site->update($data);

		return true;
	}

	public function setPlanTransfer()
	{
		// Check back account
		if ( empty($this->request['iban_account']) )
		{
			return false;
		}

		$this->data['iban_account'] = $this->request['iban_account'];

		// Check if there are changes
		if ( !$this->sitePlanHasChanged([ 'iban_account' ]) )
		{
			return true;
		}

		// Update payment info
		$data = array_merge($this->data_default, [
			'plan_id' => $this->data['plan_id'],
			'payment_interval' => $this->data['payment_interval'],
			'payment_method' => $this->data['payment_method'],
			'iban_account' => $this->data['iban_account'],
		]);

echo "<pre>";
print_r($data);
echo "</pre>";
die;

//		$this->site->update($data);



		// Send email
		$this->sendPlanChangeEmail();

		// Cancel old suscription
		$this->cancelOldSubscription();

		return true;
	}

	public function setPlanStripe()
	{
\Log::warning('[TODO] setPlanStripe');
die;
	}

	public function cancelOldSubscription()
	{
		$this->setSiteNewInfo();

		// No old plan or interval, nothing to unsubscribe from
		if ( empty($this->info['old']['plan_id']) || empty($this->info['old']['payment_interval']) )
		{
			return false;
		}



		// No old plan or free one
		$old_plan = \App\Models\Plan::find($this->info->old('plan_id'));
		if ( !$old_plan || $old_plan->is_free )
		{
			return false;
		}


echo "<pre>";
print_r($this->info);
echo "</pre>";
die;
/*
2. Cómo prorateamos las diferencias de precios entre los planes.
Si es pago mensual se cobra el nuevo importe cuando hay el nuevo cicle (proximo mes). Si el pago es anual Packageprice/12*left over months = applied discount for upgrade value
*/
		// Calculate remaining days
		// Calculate refund amount
		// Make refund API call to Stripe
		// Perform subscription cancellation
		// Set stripe_id to null
\Log::warning('[TODO] cancelOldSubscription');
	}

	public function setPlanInfo()
	{
		if ( empty($this->request['plan']) )
		{
			$this->plan = $site->plan;
		}
		else
		{
			$this->plan = \App\Models\Plan::where('code', $this->request['plan'])->first();
		}

		if ( !$this->plan )
		{
			return false;
		}

		// Check plan level (no downgrade allowed)
		if ( $this->site->plan && $this->site->plan->level > $this->plan->level )
		{
			return false;
		}

		$this->data['plan_id'] = $this->plan->id;

		return true;
	}

	public function setPaymentInfo()
	{
		if ( empty($this->request['payment_interval']) )
		{
			$this->data['payment_interval'] = $this->site->payment_interval;
		}
		else
		{
			$this->data['payment_interval'] = $this->request['payment_interval'];

		}

		if ( !$this->data['payment_interval'] )
		{
			return false;
		}

		if ( !in_array($this->data['payment_interval'], [ 'month', 'year' ]) )
		{
			return false;
		}

		if ( $this->plan && !$this->plan->is_free )
		{
			if ( empty($this->request['payment_method']) )
			{
				$this->data['payment_method'] = $this->payment_method;
			}
			else
			{
				$this->data['payment_method'] = $this->request['payment_method'];
			}

			if ( !in_array($this->data['payment_method'], array_keys(\App\Models\Plan::getPaymentOptions())) )
			{
				return false;
			}
		}
		else
		{
			$this->data['payment_method'] = false;
		}

		return true;
	}

	public function setSiteOldInfo()
	{
		$this->setSiteInfo('old',$this->site);
	}
	public function setSiteNewInfo()
	{
		$this->setSiteInfo('new',$this->site);
	}
	public function setSiteInfo($key,$site)
	{
		if ( $site->plan_id )
		{
			$plan = \App\Models\Plan::find($site->plan_id);
		}
		else
		{
			$plan = \App\Models\Plan::where('is_free',1)->first();
		}

		$this->info[$key] = [
			'plan_id' => $site->plan_id,
			'payment_interval' => $site->payment_interval,
			'payment_method' => $site->payment_method,
			'iban_account' => $site->iban_account,
			'plan_code' => $plan->code,
			'plan_name' => $plan->name,
			'plan_price' => $plan->is_free ? trans('web/plans.free') : @price(($site->payment_interval == 'month') ? $plan->price_month : $plan->price_year, [ 'decimals'=>0 ]) . " / {$site->payment_interval}",
		];
	}

	public function sendPlanChangeEmail($custom_message=false)
	{
		$this->setSiteNewInfo();

		$data = [
			'site_id' => $this->site->id,
			'site_name' => $this->site->title,
			'old_info' => $this->info['old'],
			'new_info' => $this->info['new'],
			'custom_message' => $custom_message,
		];

		$html = view('emails.admin.inform-plan-change', $data)->render();
		$css = "table, th, td { border: 1px solid black; } table { border-collapse: collapse; } th, td { text-align: left; vertical-align: top; padding: 5px; }";

		$emogrifier = new \Pelago\Emogrifier($html, $css);
		$content = $emogrifier->emogrify();
echo $content;
die;
		return \Mail::send('dummy', [ 'content' => $content ], function($message) use ($data) {
			$message->from( env('MAIL_FROM_EMAIL'), env('MAIL_FROM_NAME') );
			$message->subject("El site ID {$data['site_id']} ha modificado su plan");
			$message->to( env('EMAIL_PAYMENT_WARNINGS_TO') );
		});
	}

	public function sitePlanHasChanged($fields=false)
	{
		foreach ([ 'plan_id', 'payment_interval', 'payment_method' ] as $field) 
		{
			if ( $this->data[$field] != $this->site->$field )
			{
				return true;
			}
		}

		if ( is_array($fields) )
		{
			foreach ($fields as $field) 
			{
				if ( $this->data[$field] != $this->site->$field )
				{
					return true;
				}
			}
		}

		return false;
	}

}
