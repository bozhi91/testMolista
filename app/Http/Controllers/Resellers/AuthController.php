<?php namespace App\Http\Controllers\Resellers;

use Illuminate\Http\Request;

use App\Http\Requests;

class AuthController extends \App\Http\Controllers\Controller
{
	public function getIndex()
	{
		return redirect()->action('Resellers\AuthController@getLogin');
	}

	public function getLogin()
	{
		if ( \Auth::guard('resellers')->check() ) 
		{
			return redirect()->action('ResellersController@getIndex');
		}

		return view('resellers.auth.login');
	}
	public function postLogin()
	{
		$validated = \Auth::guard('resellers')->attempt([
			'email' => $this->request->input('email'), 
			'password' => $this->request->input('password'),
		]);

		if ( $validated && $user = \App\Models\Reseller\Auth::where('email', $this->request->input('email'))->first() )
		{
			\Auth::guard('resellers')->login($user, $this->request->input('remember'));
			return redirect()->intended( action('ResellersController@getIndex') );
		}

		return redirect()->action('Resellers\AuthController@getLogin')->with('error', trans('auth.failed'));
	}

	public function getLogout()
	{
		\Auth::guard('resellers')->logout();
		return redirect()->action('Resellers\AuthController@getLogin');
	}

}
