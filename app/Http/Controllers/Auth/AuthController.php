<?php

namespace App\Http\Controllers\Auth;

use Mail;

use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
	/*
	|--------------------------------------------------------------------------
	| Registration & Login Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles the registration of new users, as well as the
	| authentication of existing users. By default, this controller uses
	| a simple trait to add these behaviors. Why don't you explore it?
	|
	*/

	use AuthenticatesAndRegistersUsers, ThrottlesLogins;

	protected $redirectTo = '/';
	protected $redirectAfterLogout = '/';

	public function __construct()
	{
		$this->middleware('guest', ['except' => [ 'logout', 'autologin' ] ]);

		$this->redirectTo = action('AccountController@index');
		$this->redirectAfterLogout = action('Auth\AuthController@login');
	}

	/**
	* Get a validator for an incoming registration request.
	*
	* @param  array  $data
	* @return \Illuminate\Contracts\Validation\Validator
	*/
	protected function validator(array $data)
	{
		return Validator::make($data, [
			'name' => 'required|max:255',
			'email' => 'required|email|max:255|unique:users',
			'password' => 'required|confirmed|min:6',
		]);
	}

	/**
	* Create a new user instance after a valid registration.
	*
	* @param  array  $data
	* @return User
	*/
	protected function create(array $data)
	{
		// Create user
		$user = User::create([
			'name' => $data['name'],
			'email' => $data['email'],
			'locale' => \LaravelLocalization::getCurrentLocale(),
			'password' => bcrypt($data['password']),
		]);

		if ( !$user)
		{
			return $user;
		}

		// [TODO] Send welcome email
		/*
		\Mail::send('auth.emails.register', $data, function($message) use ($data)
		{
			$message->from( env('MAIL_FROM_EMAIL'), env('MAIL_FROM_NAME') );
			$message->subject( \Lang::get('auth.email.register.subject') );
			$message->to($data['email']);
		});
		*/

		return $user;
	}

	public function redirectPath()
	{
		// Current user
		//$user = \Auth::user();

		if ( env('afterLoginRedirectTo', false) )
		{
			return env('afterLoginRedirectTo');
		}

		return action('AdminController@index');
	}

	public function autologin($user_id, $autologin_token) 
	{
		// Logout current user
		if ( \Auth::check() ) 
		{
			\Auth::logout();
		}

		// Get user
		$user = \App\User::findOrFail($user_id);

		// Validate autologin token
		if ( $user->autologin_token != $autologin_token )
		{
			abort(404);
		}

		// Delete autologin token
		$user->autologin_token = null;
		$user->save();

		// Check if user allowed admin of this site
		$user->sites()->findOrFail( session('site_setup.site_id') );

		// Login as user
		\Auth::loginUsingId($user_id);

		// Redirect
		return redirect( $this->redirectPath() );
	}

	/* Disable registration */
	public function register() 
	{
		abort(404);
	}
	public function showRegistrationForm() 
	{
		abort(404);
	}

	/* Post login function */
	protected function authenticated($request, $user)
	{
		// Check allowed roles
		$roles_allowed = @array_filter( explode('|', env('loginRequiredRoles') ) );

		// If roles are not restricted
		if ( count($roles_allowed) < 1 )
		{
			return redirect()->intended( $this->redirectPath() );
		}

		// Check if user has required role
		foreach ($roles_allowed as $role)
		{
			if ( $user->hasRole($role) )
			{
				// Check if required site id
				$required_site_id = env('loginRequiredSite');
				if ( !$required_site_id || $user->sites()->where('id', $required_site_id)->count() )
				{
					return redirect()->intended( $this->redirectPath() );
				}
			}
		}

		// User does not have required role
		\Auth::guard($this->getGuard())->logout();
		return $this->sendFailedLoginResponse($request);
	}

}
