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
		return $this->logWebhook('invoice.payment_failed', $payload);
	}

	public function handleInvoicePaymentSucceeded(array $payload)
	{
		$site = $this->getUserByStripeId( $payload['data']['object']['customer'] );

		if ( $site )
		{
			$line = @reset( $payload['data']['object']['lines']['data'] );
			if ( $line['period']['end'] )
			{
				$site->update([
					'paid_until' => date('Y-m-d', $line['period']['end']),
				]);
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
