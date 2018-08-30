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
		// If site exists
		if ( $site = $this->getUserByStripeId( $payload['data']['object']['customer'] ) )
		{
			// And has paid plan
			if ( $site->plan && !$site->plan->is_free )
			{
				// And has no active subscriptions
				if ( !$site->subscribed('main') )
				{
					// Cancel subscription
					parent::handleCustomerSubscriptionDeleted($payload);

					// Update paid until
					$site->update([
						'paid_until' => date('Y-m-d'),
					]);
				}
			}
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

		// Get site
		if ( $site = $this->getUserByStripeId($payload['data']['object']['customer']) )
		{
			// Get next payment
			$next_payment_attempt = @$payload['data']['object']['next_payment_attempt'];

			// Update site paid_until
			if ( $next_payment_attempt && $next_payment_attempt > time() )
			{
				$site->update([
					'paid_until' => date('Y-m-d', $next_payment_attempt),
				]);
			}
			else
			{
				$site->update([
					'paid_until' => date("Y-m-d 00:00:00", (time()-86400)),
				]);
			}

			// Send customer email with bcc
			if ( $contact_email = $site->contact_email )
			{
				$locale_backup = \App::getLocale();
				\App::setLocale( $site->contact_locale );

				if ( $next_payment_attempt && $next_payment_attempt > time() )
				{
					// Email data
					$subject = trans('admin/emails/stripe.payment_failed_warning.subject', [ 'webname' => env('WHITELABEL_WEBNAME', 'Contromia') ]);
					$html = view("emails.site.payment-failed-warning", [
						'site' => $site,
						'next_payment_attempt' => $next_payment_attempt,
					])->render();
				}
				else
				{
					// Email data
					$subject = trans('admin/emails/stripe.payment_failed_final.subject', [ 'webname' => env('WHITELABEL_WEBNAME', 'Contromia') ]);
					$html = view("emails.site.payment-failed-final", [
						'site' => $site,
						'next_payment_attempt' => $next_payment_attempt,
					])->render();
				}

				$to = $contact_email;
				\Mail::send('dummy', [ 'content' => $html ], function($message) use ($subject, $to) {
					$message->from( env('MAIL_FROM_EMAIL'), env('MAIL_FROM_NAME') );
					$message->subject($subject);
					$message->to( $to );
					$message->bcc( env('EMAIL_PAYMENT_WARNINGS_TO', 'admin@Contromia.com') );
				});

				\App::setLocale( $locale_backup );
			}
			// Fallback admin warning email
			else
			{
				// Send warning email
				$subject = trans('admin/emails/stripe.payment_failed.subject');
				$html = view('emails.admin.inform-stripe-payment-failed', $site)->render();
				$to = env('EMAIL_PAYMENT_WARNINGS_TO', 'admin@Contromia.com');
				\Mail::send('dummy', [ 'content' => $html ], function($message) use ($subject, $to) {
					$message->from( env('MAIL_FROM_EMAIL'), env('MAIL_FROM_NAME') );
					$message->subject($subject);
					$message->to( $to );
				});
			}

		}

		return $this->logWebhook('invoice.payment_failed', $payload);
	}

	public function handleInvoicePaymentSucceeded(array $payload)
	{
		// Get site
		if ( $site = $this->getUserByStripeId( $payload['data']['object']['customer'] ) )
		{
			$total = @floatval($payload['data']['object']['total'] / 100);
			$current_subscription = $site->subscription('main');

			// If total paid > 0 and main subscription
			if ( $total > 0 && $current_subscription )
			{
				// Get customer
				\Stripe\Stripe::setApiKey( env('STRIPE_SECRET') );
				if ( $customer = \Stripe\Customer::retrieve( $site->stripe_id ) )
				{
					// If has subscriptions
					if ( @$customer->subscriptions->data )
					{
						// Iterate subscriptions
						foreach ($customer->subscriptions->data  as $subscription)
						{
							// Until we find the main subscription
							if ( $current_subscription->stripe_id == $subscription['id'] )
							{
								// Get valid dates for current subscription
								if ( @$subscription['current_period_start'] && @$subscription['current_period_end'] )
								{
									$paid_from = date('Y-m-d', $subscription['current_period_start']);
									$paid_until = date('Y-m-d', $subscription['current_period_end']);
									$paid_rate = \App\Models\CurrencyConverter::convert(1, $site->plan->currency, 'EUR');

									// Prepare site_payment
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
										\Log::error("Stripe webhook invoice.payment_succeeded: unable to create site payment\n", $payment);
										exit;
									}
									else
									{
										\App\Models\Site\Payment::saveModel($payment);
									}

									// Update site
									$site->update([
										'paid_until' => $paid_until,
									]);

									// Send customer email with bcc
									if ( $contact_email = $site->contact_email )
									{
										$locale_backup = \App::getLocale();
										\App::setLocale( $site->contact_locale );

										$subject = trans('admin/emails/stripe.payment_succeeded.subject', [ 'webname' => env('WHITELABEL_WEBNAME', 'Contromia') ]);
										$html = view("emails.site.payment-received", [
											'site' => $site,
											'paid_from' => $paid_from,
											'paid_until' => $paid_until,
										])->render();
										$to = $contact_email;
										\Mail::send('dummy', [ 'content' => $html ], function($message) use ($subject, $to) {
											$message->from( env('MAIL_FROM_EMAIL'), env('MAIL_FROM_NAME') );
											$message->subject($subject);
											$message->to( $to );
											$message->bcc( env('EMAIL_PAYMENT_WARNINGS_TO', 'admin@Contromia.com') );
										});

										\App::setLocale( $locale_backup );
									}
									// Send admin warning email
									else
									{
										$subject = trans('corporate/signup.email.stripe.subject');
										$html = view('emails.admin.inform-stripe-payment', $site)->render();
										$to = env('EMAIL_PAYMENT_WARNINGS_TO', 'admin@Contromia.com');
										\Mail::send('dummy', [ 'content' => $html ], function($message) use ($subject, $to) {
											$message->from( env('MAIL_FROM_EMAIL'), env('MAIL_FROM_NAME') );
											$message->subject($subject);
											$message->to( $to );
										});
									}
								}
								break;
							}
						}
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

		return (new $model)->with('plan')->whereNotNull('stripe_id')->where('stripe_id', $stripeId)->first();
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
