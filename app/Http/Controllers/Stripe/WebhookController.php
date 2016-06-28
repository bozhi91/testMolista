<?php namespace App\Http\Controllers\Stripe;

use Laravel\Cashier\Http\Controllers\WebhookController as BaseController;

class WebhookController extends BaseController
{
	public function handleCustomerCreated(array $payload)
	{
		return logWebhook('customer.created', $payload);
	}

	public function handleCustomerDeleted(array $payload)
	{
		return logWebhook('customer.deleted', $payload);
	}

	public function handleCustomerUpdated(array $payload)
	{
		return logWebhook('customer.updated', $payload);
	}

	public function handleCustomerSubscriptionCreated(array $payload)
	{
		return logWebhook('customer.subscription.created', $payload);
	}

	public function handleCustomerSubscriptionDeleted(array $payload)
	{
		parent::handleCustomerSubscriptionDeleted($payload);
		
		return logWebhook('customer.subscription.deleted', $payload);
	}
	
	public function handleCustomerSubscriptionTrialWillEnd(array $payload)
	{
		return logWebhook('customer.subscription.trial_will_end', $payload);
	}

	public function handleCustomerSubscriptionUpdated(array $payload)
	{
		return logWebhook('customer.subscription.updated', $payload);
	}

	public function handleInvoiceCreated(array $payload)
	{
		return logWebhook('invoice.created', $payload);
	}
	
	public function handleInvoicePaymentFailed(array $payload)
	{
		return logWebhook('invoice.payment_failed', $payload);
	}

	public function handleInvoicePaymentSucceeded(array $payload)
	{
		return logWebhook('invoice.payment_succeeded', $payload);
	}

	public function handleInvoiceUpdated(array $payload)
	{
		return logWebhook('invoice.updated', $payload);
	}


	protected function logWebhook($event, $payload)
	{
		\Log::info("Stripe\WebhookController -> {$event}");
		\Log::info($payload);
		return new Response('Webhook Handled', 200);
	}

}
