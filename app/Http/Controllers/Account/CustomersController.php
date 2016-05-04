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

	public function create()
	{
		return view('account.customers.create');
	}

	public function store()
	{
		$validator = \Validator::make($this->request->all(), $this->getRequiresFields());
		if ($validator->fails()) 
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		$customer = $this->site->customers()->create([
			'email' => $this->request->get('email'),
			'locale' => $this->request->get('locale'),
			'created_by' => \Auth::user()->id,
		]);

		if ( !$customer )
		{
			return redirect()->back()->withInput()->with('error',trans('general.messages.error'));
		}

		return $this->update($customer->email);
	}

	public function update($email)
	{
		$customer = $this->site->customers()->where('email', $email)->first();
		if ( !$customer )
		{
			abort(404);
		}

		$validator = \Validator::make($this->request->all(), $this->getRequiresFields($customer->id));
		if ($validator->fails()) 
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		$customer->update([
			'first_name' => $this->request->get('first_name'),
			'last_name' => $this->request->get('last_name'),
			'email' => $this->request->get('email'),
			'phone' => $this->request->get('phone'),
			'locale' => $this->request->get('locale'),
		]);

		return redirect()->action('Account\CustomersController@show', urlencode($customer->email))->with('success', trans('account/customers.message.saved'));
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

	protected function getRequiresFields($id=false)
	{
		$locales = array_keys( \App\Session\Site::get('locales_tabs') );

		$fields = [
			'first_name' => 'required',
			'last_name' => 'required',
			'email' => "required|email|unique:customers,email".($id ? ",{$id}" : ''),
			'phone' => 'required',
			'locale' => 'required|in:'.implode(',',$locales),
		];

		return $fields;		
	}
}
