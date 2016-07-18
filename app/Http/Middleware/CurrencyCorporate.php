<?php namespace App\Http\Middleware;

use Closure;

class CurrencyCorporate
{
	public function handle($request, Closure $next)
	{
		// Get currency code from geolocation
		$currency_code = \App\Session\Geolocation::get('config.currency');
		if ( !$currency_code )
		{
			$currency_code = env('CURRENCY_DEFAULT','USD');
		}

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
