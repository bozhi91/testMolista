<?php namespace App\Http\Controllers\Stripe;

use Laravel\Cashier\Http\Controllers\WebhookController as BaseController;

class WebhookController extends BaseController
{
	public function handleCustomerCreated(array $payload)
	{
		return response('Webhook Handled', 200);
	}

	public function handleCustomerDeleted(array $payload)
	{
		return $this->logWebhook('customer.deleted', $payload);
	}

	public function handleCustomerUpdated(array $payload)
	{
		return response('Webhook Handled', 200);
	}

	public function handleCustomerSubscriptionCreated(array $payload)
	{
		return $this->logWebhook('customer.subscription.created', $payload);
	}

	public function handleCustomerSubscriptionDeleted(array $payload)
	{
		parent::handleCustomerSubscriptionDeleted($payload);

		$site = $this->getUserByStripeId( $payload['data']['object']['customer'] );

		if ( $site )
		{
			$site->update([
				'paid_until' => date('Y-m-d'),
			]);
		}

		return $this->logWebhook('customer.subscription.deleted', $payload);
	}

	public function handleCustomerSubscriptionTrialWillEnd(array $payload)
	{
		return $this->logWebhook('customer.subscription.trial_will_end', $payload);
	}

	public function handleCustomerSubscriptionUpdated(array $payload)
	{
		return $this->logWebhook('customer.subscription.updated', $payload);
	}

	public function handleInvoiceCreated(array $payload)
	{
		return $this->logWebhook('invoice.created', $payload);
	}

	public function handleInvoicePaymentFailed(array $payload)
	{
		$site = $this->getUserByStripeId($payload['data']['object']['customer']);

		if ($site)
		{
			// Send warning email
			$subject = trans('admin/emails/stripe.payment_failed.subject');
			$html = view('emails.admin.inform-stripe-payment-failed', $site)->render();
			$to = env('EMAIL_PAYMENT_WARNINGS_TO', 'admin@molista.com');
			\Mail::send('dummy', [ 'content' => $html ], function($message) use ($subject, $to) {
				$message->from( env('MAIL_FROM_EMAIL'), env('MAIL_FROM_NAME') );
				$message->subject($subject);
				$message->to( $to );
			});
		}

		return $this->logWebhook('invoice.payment_failed', $payload);
	}

	public function handleInvoicePaymentSucceeded(array $payload)
	{
		$site = $this->getUserByStripeId( $payload['data']['object']['customer'] );

		if ( $site )
		{
			$total = @floatval($payload['data']['object']['total'] / 100);
			$current_subscription = $site->subscriptions()->where('name','main')->first();

			if ( $total && $current_subscription )
			{
				// Get valid dates for current subscription
				\Stripe\Stripe::setApiKey( env('STRIPE_SECRET') );
				$customer = \Stripe\Customer::retrieve( $site->stripe_id );
				if ( $customer )
				{
					$paid_from = false;
					$paid_until = false;

					$subscriptions = @$customer->subscriptions->data;

					if ( $subscriptions)
					{
						foreach ($subscriptions  as $subscription)
						{
							if ( $current_subscription->stripe_id == $subscription['id'] )
							{
								$paid_from = date('Y-m-d', $subscription['current_period_start']);
								$paid_until = date('Y-m-d', $subscription['current_period_end']);
							}
						}
					}

					if ( $paid_from && $paid_until )
					{
						$paid_rate = \App\Models\CurrencyConverter::convert(1, $site->plan->currency, 'EUR');

						// Generar site_payment
						$payment = $site->preparePaymentData([
							'trigger' => 'Stripe webhook (handleInvoicePaymentSucceeded)',
							'paid_from' => $paid_from,
							'paid_until' => $paid_until,
							'payment_method' =>  'stripe',
							'payment_amount' => $total,
							'payload' => $payload,
						]);
						$validator = \App\Models\Site\Payment::getCreateValidator($payment);
						if ($validator->fails())
						{
							\Log::error("Stripe webhook invoice.payment_succeeded: unable to create site payment\n",$payment);
						}
						else
						{
							\App\Models\Site\Payment::saveModel($payment);
						}

						// Update site
						$site->update([
							'paid_until' => $paid_until,
						]);

						// Send warning email
						$subject = trans('corporate/signup.email.stripe.subject');
						$html = view('emails.admin.inform-stripe-payment', $site)->render();
						$to = env('EMAIL_PAYMENT_WARNINGS_TO', 'admin@molista.com');
						\Mail::send('dummy', [ 'content' => $html ], function($message) use ($subject, $to) {
							$message->from( env('MAIL_FROM_EMAIL'), env('MAIL_FROM_NAME') );
							$message->subject($subject);
							$message->to( $to );
						});
					}
				}
			}
		}

		return $this->logWebhook('invoice.payment_succeeded', $payload);
	}

	public function handleInvoiceUpdated(array $payload)
	{
		return $this->logWebhook('invoice.updated', $payload);
	}


	protected function getUserByStripeId($stripeId)
	{
		$model = getenv('STRIPE_MODEL') ?: config('services.stripe.model');

		return (new $model)->whereNotNull('stripe_id')->where('stripe_id', $stripeId)->first();
	}


	protected function logWebhook($event, $payload)
	{
		$site = $this->getUserByStripeId( @$payload['data']['object']['customer'] );

		if ( $site )
		{
			$site->webhooks()->create([
				'source' => 'stripe',
				'event' => $event,
				'data' => $payload,
			]);
		}
		else
		{
			\Log::info("Stripe webhook {$event}: unable to relate to site");
			\Log::info($payload);
		}

		return response('Webhook Handled', 200);
	}

}
