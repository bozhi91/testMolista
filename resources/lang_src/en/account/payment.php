<?php
	return [
		'plan.h1' => 'Current plan',
		'plan.show' => 'Show plans',
		'plan.upgrade' => 'Upgrade plan',
		'plan.upgrade.simple' => 'Upgrade',
		'plan.price' => 'Price',
		'plan.valid.from' => 'Start date',
		'plan.next.charge' => 'Next charge',
		'plan.last.charge.attempt' => 'Last charge attempt',
		'plan.last.charge.warning' => '<p>All attempts to make the payment have failed.</p>
										<p>Please update your card details and try to make the payment again.</p>
										<p>If the problem persists, contact us.</p>',

		'method.h1' => 'Payment method',
		'method.stripe' => 'Credit card',
		'method.stripe.update' => 'Update',
		'method.stripe.retry' => 'Retry payment',
		'method.transfer' => 'Direct debit',
		'method.change' => 'Change method',
		'method.account' => 'Bank account',

		'upgrade.select' => 'Select your plan',
		'upgrade.success.plan' => 'Your plan was successfully updated.',
		'upgrade.success.payment' => 'The payment information was successfully updated.',

		'data' => 'My information',

		'plans' => 'My plan',
		'plans.pending.transfer' => '<p>We are still confirming the payment information you provided us when requesting the plan upgrade:</p>
									<ul>
										<li>Plan: :plan</li>
										<li>:paymethod</li>
									</ul>
									<p>We will let you know as soon as possible.</p>',
		'plans.pending.stripe' => '<p>You have a plan upgrade request pending:</p>
									<ul>
										<li>Plan: :plan</li>
										<li>:paymethod</li>
									</ul>',
		'plans.pending.button' => 'Pay now',
		'plans.pending.cancel' => 'Cancel request',
		'plans.cancel.warning' => 'Are you sure you want to cancel this request?',
		'plans.cancel.success' => 'The request was successfully canceled',

		'invoices' => 'My invoices',
		'invoices.empty' => 'No invoices found.',
		'invoices.uploaded_at' => 'Date',
		'invoices.reference' => 'Referene',
		'invoices.amount' => 'Amount',

		'invoicing.title' => 'Invoicing data',
		'invoicing.created' => 'The plan upgrade request is being processed',
		'invoicing.updated' => 'The plan upgrade was successfully processed',
		'invoicing.transfer.created' => '<h4>Thanks for choosing the direct debit payment method.</h4><p>We have received all the information we require to initiate the payment process.</p><p>We will contact you as soon as possible.</p>',
		'invoicing.transfer.intro' => '<p>We are now verifying the payment information you provided. We will let you know when we are done with it and you will be able to enjoy your chose plan (:plan - :price_text).</p>',

		'cc.update.title' => 'Update credit card details',
		'cc.update.intro' => '<p>For security reasons, we do not store your credit card information in our database.</p>
								<p>To process our payments we use Stripe, a very secure payment gateway that utilizes several tools to prevent fraud.</p>
								<p>For more information on Stripe, please visit <a href="https://stripe.com" target="_blank">www.stripe.com</a>.</p>',
		'cc.update.button' => 'Continue',
		'cc.update.label' => 'Update card details',
		'cc.update.success' => 'Your credit card details were successfully updated',
	];
