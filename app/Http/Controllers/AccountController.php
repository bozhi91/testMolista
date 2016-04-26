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

		if ( $site_id = \App\Session\Site::get('site_id', false) )
		{
			$this->site = \App\Site::findOrFail( $site_id );
		}
	}

	public function index()
	{
		\View::share('submenu_section', 'home');
		return view('account.index');
	}

	public function updateProfile()
	{
		$fields = [
			'name' => 'required|max:255',
			'email' => "required|email|max:255|unique:users,email,{$this->auth->user()->id},id",
			'locale' => 'required|string|in:'.implode(',',\LaravelLocalization::getSupportedLanguagesKeys()),
			'password' => 'min:6',
		];
		$validator = \Validator::make($this->request->all(), $fields);
		if ($validator->fails()) 
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		foreach ($fields as $key => $def)
		{
			if ( $key == 'password' )
			{
				if ( $this->request->get('password') )
				{
					$this->auth->user()->password = bcrypt($this->request->get('password'));

				}
				continue;
			}

			$this->auth->user()->$key = $this->request->get($key);
		}

		$this->auth->user()->save();

		return redirect()->back()->with('success', trans('account/profile.message.saved'));
	}

}
