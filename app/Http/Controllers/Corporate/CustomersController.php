<?php namespace App\Http\Controllers\Corporate;

use Illuminate\Http\Request;

use App\Http\Requests;

class CustomersController extends \App\Http\Controllers\CorporateController
{
	public function getIndex()
	{
		$action = old('action', 'login');

		return view('corporate.customers.index', compact('action'));
	}

	public function postIndex()
	{
		// Validate subdomain
		$fields = [
			'subdomain' => 'required|exists:sites,subdomain',
		];
		$validator = \Validator::make($this->request->all(), $fields);
		if ( $validator->fails() ) 
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		// Get site
		$site = \App\Site::where('subdomain', $this->request->input('subdomain'))->first();

		// Basic response
		switch ( $this->request->input('action') )
		{
			case 'remember':
				return redirect()->to( $site->remember_password_url );
			case 'login':
				break;
			default:
				return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		// Validate data
		$fields = [
			'email' => 'required|string',
			'password' => 'required|string',
		];
		$validator = \Validator::make($this->request->all(), $fields);
		if ( $validator->fails() ) 
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		// Validate credentials
		if ( ! \Auth::validate( $this->request->only('email','password') ))
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		// Get user for this site
		$user = $site->users()->where('email', $this->request->input('email'))->first();
		if ( !$user )
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		$action = action('AccountController@index', [ 'autologin_token' =>$user->getUpdatedAutologinToken() ]);

		return redirect()->to( $site->getSiteFullUrl($action) );
	}

}
