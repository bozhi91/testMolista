<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AuthenticateResellers
{
	public function handle($request, Closure $next)
	{
		$guard = 'resellers';

		if ( Auth::guard($guard)->guest() ) 
		{
			return redirect()->guest( action('Resellers\AuthController@getLogin') );
		}

		$reseller_user = \App\Models\Reseller::findOrFail( Auth::guard($guard)->user()->id );

		$request->attributes->add([
			'reseller' => $reseller_user,
		]);

		\View::share('reseller_user', $reseller_user);

		return $next($request);
	}
}