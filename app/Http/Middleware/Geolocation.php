<?php namespace App\Http\Middleware;

use Closure;

class Geolocation
{
	public function handle($request, Closure $next)
	{
		$geolocation = \App\Session\Geolocation::all();

		if ( !$geolocation )
		{
			$geolocation = \GeoIP::getLocation();
			// $geolocation = \GeoIP::getLocation('146.83.255.255'); Chile
		}

		// Get country information
		$country = \App\Models\Geography\Country::where('code',$geolocation['isoCode'])->first();
		if ( !$country )
		{
			$country = \App\Models\Geography\Country::where('code','US')->first();
		}

		// Add to geolocation
		$geolocation['config'] = $country->toArray();
		$geolocation['config']['items_folder'] = $country->items_folder;

		// Save in session
		\App\Session\Geolocation::replace($geolocation);

		// Pass to request
		$request->attributes->add([
			'geolocation' => $geolocation,
		]);

		return $next($request);
	}
}
