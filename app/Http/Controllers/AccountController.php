<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class AccountController extends Controller
{
	protected $site;

	public function __initialize()
	{
		parent::__initialize();
		\View::share('menu_section', 'account');
		\View::share('hide_advanced_search_modal', true);
	}

	public function index()
	{
		\View::share('submenu_section', 'home');

		$pending_request = $this->site->planchanges()->pending()->first();

		return view('account.index', compact('pending_request'));
	}

	public function updateProfile()
	{
		$fields = \App\User::getFields( $this->auth->user()->id );
		$validator = \Validator::make($this->request->all(), $fields);
		if ( $validator->fails() )
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		$saved = \App\User::saveModel($this->request->all(), $this->auth->user()->id);
		if ( !$saved )
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		return redirect()->back()->with('success', trans('account/profile.message.saved'));
	}

}
