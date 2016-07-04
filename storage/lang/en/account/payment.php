<?php
	return [
		'plan.h1' => 'Current plan',
		'plan.show' => 'Show plans',
		'plan.upgrade' => 'Upgrade plan',
		'plan.upgrade.simple' => 'Upgrade',

		'method.h1' => 'Payment method',
		'method.stripe' => 'Credit card',
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

		'invoices' => 'My invoices',
		'invoices.empty' => 'No invoices found.',
		'invoices.uploaded_at' => 'Date',
		'invoices.reference' => 'Referene',
		'invoices.amount' => 'Amount',

		'invoicing.title' => 'Invoicing data',
		'invoicing.created' => 'The plan upgrade request is being processed',
		'invoicing.updated' => 'The plan upgrade was successfully processed',
		'invoicing.transfer.intro' => '<p>We are now verifying the payment information you provided. We will let you know when we are done with it and you will be able to enjoy your chose plan (:plan - :price_text).</p>',
	];
