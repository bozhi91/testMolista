<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\WebController;

class CustomersController extends WebController
{
	public function __initialize()
	{
		$this->middleware([ "site.customer.auth" ], [ 'except' => [ 'getRegister', 'postRegister', 'getLogin', 'postLogin', 'getCheck', 'getLogout'] ]);
		$this->middleware([ "site.customer.guest" ], [ 'only' => [ 'getRegister', 'postRegister', 'getLogin', 'postLogin' ] ]);

		parent::__initialize();
	}

	public function getIndex()
	{
		return view('web.customers.index');
	}

	public function getRegister()
	{
		if ( !$this->site->customer_register )
		{
			abort(404);
		}

		return view('web.customers.register');
	}
	public function postRegister()
	{
		if ( !$this->site->customer_register )
		{
			abort(404);
		}

		$fields = [
			'first_name' => 'required',
			'last_name' => 'required',
			'email' => "required|email|unique:customers,email",
			'password' => 'required|min:6',
			'phone' => 'required|min:6',
		];
		$validator = \Validator::make($this->request->all(), $fields);
		if ($validator->fails()) 
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		$customer = $this->site->customers()->create([
			'first_name' => $this->request->get('first_name'),
			'last_name' => $this->request->get('last_name'),
			'email' => $this->request->get('email'),
			'password' => bcrypt($this->request->get('password')),
			'phone' => $this->request->get('phone'),
			'locale' => \LaravelLocalization::getCurrentLocale(),
			'validated' => 1,
		]);

		if ( !$customer )
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		\SiteCustomer::login($this->request->get('email'), $this->request->get('password'), $this->site->id);

		return redirect()->action('Web\CustomersController@getIndex')->with('success', trans('web/customers.register.success'));
	}

	public function getLogin()
	{
		return view('web.customers.login');
	}
	public function postLogin()
	{
		// Login attempt
		if ( \SiteCustomer::login($this->request->get('email'), $this->request->get('password'), $this->site->id) )
		{
			return redirect()->intended( action('Web\CustomersController@getIndex') );
		}

		return redirect()->back()->withInput()->with('error', trans('web/customers.login.error'));
	}

	public function getLogout()
	{
		\SiteCustomer::flush();
		return redirect()->action('Web\CustomersController@getIndex');
	}

	public function getCheck($type)
	{
		$result = false;

		switch ($type)
		{
			case 'email':
				$result = ( $this->site->customers()->where('email', $this->request->get('email'))->count() == 0 );
				break;
		}

		echo $result ? 'true' : 'false';
	}

}
