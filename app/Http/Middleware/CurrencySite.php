<?php namespace App\Http\Middleware;

use Closure;

class CurrencySite
{
	public function handle($request, Closure $next)
	{

		$site = $request->get('site');

		$currency_code = $site ? $site->site_currency : env('CURRENCY_DEFAULT','USD');

		// Get currency info
		$currency = \App\Models\Currency::where('code',$currency_code)->first();

		// Pass to request
		$request->attributes->add([
			'currency' => $currency,
		]);

		// Save in session
		\App\Session\Currency::replace($currency->toArray());

		return $next($request);
	}
}
