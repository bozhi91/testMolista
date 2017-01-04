<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class AccountController extends Controller
{
	public function __initialize()
	{
		parent::__initialize();
		\View::share('menu_section', 'account');
		\View::share('hide_advanced_search_modal', true);
	}

	public function index()
	{
		\View::share('submenu_section', 'profile');

		$current_tab = 'data';

		return view('account.index', compact('current_tab'));
	}

	public function updateProfile()
	{
		$fields = \App\User::getFields( $this->auth->user()->id );
		$validator = \Validator::make($this->request->all(), $fields);
		if ( $validator->fails() )
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		$user = \App\User::saveModel($this->request->all(), $this->auth->user()->id);
		if ( !$user )
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		// Update user info in ticketing system
		$this->site->ticket_adm->associateUsers([ $user ]);

		return redirect()->back()->with('success', trans('account/profile.message.saved'));
	}

}
