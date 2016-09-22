<?php
	return [
		'empty' => 'No payment requests found',

		'site' => 'Site',
		'plan' => 'Plan',
		'payment.interval' => 'Payment',
		'payment.method' => 'Method',
		'status' => 'Status',
		'created' => 'Date',
		'paid.amount' => 'Amount paid',
		'paid.from' => 'Mark as valid from',
		'paid.until' => 'Mark as valid until',

		'edit.title' => 'Payment request',
		'edit.request' => 'Request',
		'edit.history' => 'History',
		'edit.history.empty' => 'No historical data was found',
		'edit.data.current' => 'Current info',
		'edit.data.requested' => 'Requested info',

		'button.accept' => 'Accept request',
		'button.reject' => 'Reject request',

		'message.rejected' => 'The payment request has been rejectet',
		'message.accepted' => 'The payment request has been accepted',

		'reject.reason' => 'Reson for rejection',
		'reject.reason.helper' => '<p>The reason will be included in the email sent to the site owner.</p><p>:language will be user for this email.</p>',
		'reject.subject' => 'Your plan change request has been denied',
		'reject.body' => '<p>Hello :username,</p>
							<p>We are sorry to communicate you that your request for a plan change for your website :siteurl has been denied.</p>
							<div>:reason</div>',
		'accept.subject' => 'Your request for a plan change was successful processed',
		'accept.body' => '<p>Hello :username,</p>
							<p>We are pleased to inform you that your plan change request was successful processed.</p>
							<p>From now on, you can enjoy your new plan:</p>
							<ul>
								<li>Plan: :plan</li>
								<li>:payment</li>
								<li>Site: :siteurl</li>
							</ul>',
	];
