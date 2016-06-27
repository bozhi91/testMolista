<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
	/**
	* The URIs that should be excluded from CSRF verification.
	*
	* @var array
	*/
	protected $except = [
		/* Stripe webhooks */
		'stripe/*',
		'*/stripe/*',
		/* Widgets */
		'account/site/widgets/*',
		'*/account/site/widgets/*',
		/* Pages */
		'account/site/pages/*',
		'*/account/site/pages/*',
	];

}
