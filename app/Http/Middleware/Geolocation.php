<?php namespace App\Http\Middleware;

use Closure;

class Geolocation
{
	public function handle($request, Closure $next)
	{
		$geolocation = \App\Session\Geolocation::all();

		if ( !$geolocation )
		{
			$geolocation = \GeoIP::getLocation('177.231.161.243');
			\App\Session\Geolocation::replace($geolocation);
		}

		$request->attributes->add([
			'geolocation' => $geolocation,
		]);

		return $next($request);
	}
}
