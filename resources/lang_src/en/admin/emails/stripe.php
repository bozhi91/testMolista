<?php
	return [
		'payment_succeeded.subject' => 'Payment confirmation - :webname',
		'payment_succeeded.body' => '<p>Hello :name:</p>
										<p>We are glad to inform you that we has successfully received the payment for your :plan plan, that corresponds to the period between :start and :end.</p>',

		'payment_failed_warning.subject' => 'Payment error - :webname',
		'payment_failed_warning.body' => '<p>Hello :name:</p>
										<p>We inform you that we were unable to process the payment for your :plan plan of :webname.</p>
										<p>We will retry the payment on :next_attempt.</p>',

		'payment_failed_final.subject' => 'Critical payment error - :webname',
		'payment_failed_final.body' => '<p>Hello :name:</p>
										<p>We inform you that, after several attempts, we were unable to process the payment for your :plan plan of :webname, so we have proceed to disable your website ":sitename".</p>
										<p>If you wish to reactivate it, please contact us.</p>',
	];
