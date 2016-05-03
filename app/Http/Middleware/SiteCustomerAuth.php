<?php

namespace App\Http\Middleware;

use Closure;

class SiteCustomerAuth
{
	public function handle($request, Closure $next)
	{
		if ( \SiteCustomer::guest() )
		{
			return redirect()->guest( action('Web\CustomersController@getLogin') );
		}

		return $next($request);
	}
}
