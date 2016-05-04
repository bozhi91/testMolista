<?php

namespace App\Http\Middleware;

use Closure;

class SiteCustomerGuest
{
	public function handle($request, Closure $next)
	{
		if ( \SiteCustomer::check() )
		{
			return redirect()->guest( action('Web\CustomersController@getIndex') );
		}

		return $next($request);
	}
}
