<?php

namespace App\Http\Controllers\Account;

use Illuminate\Http\Request;

use App\Http\Requests;

class CustomersController extends \App\Http\Controllers\AccountController
{
	public function __initialize()
	{
		parent::__initialize();
		\View::share('submenu_section', 'customers');
	}

	public function index()
	{
		$query = $this->site->customers();

		// Filter by name
		if ( $this->request->get('full_name') )
		{
			$query->withFullName( $this->request->get('full_name') );
		}

		// Filter by email
		if ( $this->request->get('email') )
		{
			$query->where('customers.email', 'like', "%{$this->request->get('email')}%");
		}

		$customers = $query->orderBy('created_at','desc')->paginate( $this->request->get('limit', \Config::get('app.pagination_perpage', 10)) );

		$this->set_go_back_link();

		return view('account.customers.index', compact('customers'));
	}

	public function show($email)
	{
		$customer = $this->site->customers()->where('email', $email)->first();
		if ( !$customer )
		{
			abort(404);
		}

		return view('account.customers.show', compact('customer'));
	}

}
