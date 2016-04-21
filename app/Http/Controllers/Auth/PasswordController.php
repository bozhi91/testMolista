<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;

use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Password;

class PasswordController extends Controller
{
	/*
	|--------------------------------------------------------------------------
	| Password Reset Controller
	|--------------------------------------------------------------------------
	|
	| This controller is responsible for handling password reset requests
	| and uses a simple trait to include this behavior. You're free to
	| explore this trait and override any methods you wish to tweak.
	|
	*/

	use ResetsPasswords;

	protected $redirectPath = '/admin';

	/**
	* Create a new password controller instance.
	*
	* @return void
	*/
	public function __construct()
	{
		$this->middleware('guest');

		if ( env('afterLoginRedirectTo', false) )
		{
			$this->redirectPath = env('afterLoginRedirectTo');
		}
	}

	public function sendResetLinkEmail(Request $request)
	{
		$validator = \Validator::make($request->all(), [
			'email' => 'required|email',
		]);
		if ($validator->fails()) 
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		// Valid user flag
		$valid_user = false;

		// Get user by email
		$user = \App\User::where('email', $request->get('email'))->first();

		// Check allowed roles
		$roles_allowed = @array_filter( explode('|', env('loginRequiredRoles') ) );

		// If roles are not restricted
		if ( count($roles_allowed) < 1 )
		{
			$valid_user = true;
		}
		else
		{
			if ( $user )
			{
				// Check if user has required role
				foreach ($roles_allowed as $role)
				{
					if ( $user->hasRole($role) )
					{
						$valid_user = true;
						break;
					}
				}
			}
		}

		// Check if required site id
		if ( $valid_user && $required_site_id = env('loginRequiredSite') )
		{
			if ( $user->sites()->where('id', $required_site_id)->count() < 1 )
			{
				$valid_user = false;
			}
		}

		// Error response
		if ( ! $valid_user )
		{
			return $this->getSendResetLinkEmailFailureResponse('passwords.user');
		}

		$broker = false;

		$response = Password::broker($broker)->sendResetLink($request->only('email'), function (Message $message) {
			$message->subject($this->getEmailSubject());
		});

		switch ($response) {
			case Password::RESET_LINK_SENT:
				return $this->getSendResetLinkEmailSuccessResponse($response);
			case Password::INVALID_USER:
			default:
				return $this->getSendResetLinkEmailFailureResponse($response);
		}
	}
    protected function getSendResetLinkEmailSuccessResponse($response)
    {
        return redirect()->back()->with('status', trans($response));
    }
    protected function getSendResetLinkEmailFailureResponse($response)
    {
        return redirect()->back()->withErrors(['email' => trans($response)]);
    }

}
