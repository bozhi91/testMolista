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
			'phone' => 'required',
		];
		$validator = \Validator::make($this->request->all(), $fields);
		if ($validator->fails()) 
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		$customer = $this->site->customers()->create([
			'first_name' => $this->request->input('first_name'),
			'last_name' => $this->request->input('last_name'),
			'email' => $this->request->input('email'),
			'password' => bcrypt($this->request->input('password')),
			'phone' => $this->request->input('phone'),
			'locale' => \LaravelLocalization::getCurrentLocale(),
			'validated' => 1,
		]);

		if ( !$customer )
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		\SiteCustomer::login($this->request->input('email'), $this->request->input('password'), $this->site->id);

		return redirect()->action('Web\CustomersController@getIndex')->with('success', trans('web/customers.register.success'));
	}

	public function getLogin()
	{
		return view('web.customers.login');
	}
	public function postLogin()
	{
		// Login attempt
		if ( \SiteCustomer::login($this->request->input('email'), $this->request->input('password'), $this->site->id) )
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
				$query = $this->site->customers()->where('email', $this->request->input('email'));
				if ( $this->request->input('id') )
				{
					$query->where('id','!=',$this->request->input('id'));
				}
				$result = ( $query->count() == 0 );
				break;
		}

		echo $result ? 'true' : 'false';
	}

}
