<?php namespace App\Http\Controllers\Stripe;

use Laravel\Cashier\Http\Controllers\WebhookController as BaseController;

class WebhookController extends BaseController
{
	public function handleCustomerCreated($payload)
	{
		return logWebhook('customer.created', $payload);
	}

	public function handleCustomerDeleted($payload)
	{
		return logWebhook('customer.deleted', $payload);
	}

	public function handleCustomerUpdated($payload)
	{
		return logWebhook('customer.updated', $payload);
	}

	public function handleCustomerSubscriptionCreated($payload)
	{
		return logWebhook('customer.subscription.created', $payload);
	}

	public function handleCustomerSubscriptionDeleted($payload)
	{
		parent::handleCustomerSubscriptionDeleted($payload);
		
		return logWebhook('customer.subscription.deleted', $payload);
	}
	
	public function handleCustomerSubscriptionTrialWillEnd($payload)
	{
		return logWebhook('customer.subscription.trial_will_end', $payload);
	}

	public function handleCustomerSubscriptionUpdated($payload)
	{
		return logWebhook('customer.subscription.updated', $payload);
	}

	public function handleInvoiceCreated($payload)
	{
		return logWebhook('invoice.created', $payload);
	}
	
	public function handleInvoicePaymentFailed($payload)
	{
		return logWebhook('invoice.payment_failed', $payload);
	}

	public function handleInvoicePaymentSucceeded($payload)
	{
		return logWebhook('invoice.payment_succeeded', $payload);
	}

	public function handleInvoiceUpdated($payload)
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
