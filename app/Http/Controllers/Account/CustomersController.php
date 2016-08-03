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
		$query = $this->site->customers()->with('queries');

		// Filter by name
		if ( $this->request->input('full_name') )
		{
			$query->withFullName( $this->request->input('full_name') );
		}

		// Filter by email
		if ( $this->request->input('email') )
		{
			$query->where('customers.email', 'like', "%{$this->request->input('email')}%");
		}

		$customers = $query->orderBy('created_at','desc')->paginate( $this->request->input('limit', \Config::get('app.pagination_perpage', 10)) );

		$this->set_go_back_link();

		return view('account.customers.index', compact('customers'));
	}

	public function create()
	{
		return view('account.customers.create');
	}

	public function store()
	{
		$validator = \Validator::make($this->request->all(), $this->getRequiredFields());
		if ($validator->fails()) 
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		$customer = $this->site->customers()->create([
			'first_name' => $this->request->input('first_name'),
			'last_name' => $this->request->input('last_name'),
			'email' => $this->request->input('email'),
			'phone' => $this->request->input('phone'),
			'locale' => $this->request->input('locale'),
			'created_by' => \Auth::user()->id,
		]);

		if ( !$customer )
		{
			return redirect()->back()->withInput()->with('error',trans('general.messages.error'));
		}

		if ( $this->request->input('ajax') )
		{
			$options = [];
			foreach ($this->site->customers_options as $key => $value)
			{
				$options[] = '<option value="' . $key . '"' . ($key==$customer->id ? 'selected="selected"' : '') . '>' . $value .'</option>';
			}
			return view('account.customers.store', compact('options'));
		}

		return redirect()->action('Account\CustomersController@show', urlencode($customer->email))->with('success', trans('account/customers.message.saved'));
	}

	public function update($email)
	{
		$customer = $this->site->customers()->where('email', $email)->first();
		if ( !$customer )
		{
			abort(404);
		}

		$validator = \Validator::make($this->request->all(), $this->getRequiredFields($customer->id));
		if ($validator->fails()) 
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		$customer->update([
			'first_name' => $this->request->input('first_name'),
			'last_name' => $this->request->input('last_name'),
			'email' => $this->request->input('email'),
			'phone' => $this->request->input('phone'),
			'locale' => $this->request->input('locale'),
		]);

		return redirect()->action('Account\CustomersController@show', urlencode($customer->email))->with('success', trans('account/customers.message.saved'));
	}

	public function show($email)
	{
		$customer = $this->site->customers()->with('queries')->where('email', $email)->first();
		if ( !$customer )
		{
			abort(404);
		}

		$profile = $customer->current_query;

		$countries = $this->site->enabled_countries;
		if ( $country_id = @$profile->country_id ? $profile->country_id : $this->site->country_id )
		{
			$states = \App\Models\Geography\State::enabled()->where('country_id', $country_id)->lists('name','id')->all();
		}
		if ( @$profile->state_id )
		{
			$cities = \App\Models\Geography\City::enabled()->where('state_id', $profile->state_id)->lists('name','id')->all();
		}

		$modes = \App\Property::getModeOptions();
		$types = \App\Property::getTypeOptions();
		$services = \App\Models\Property\Service::withTranslations()->enabled()->orderBy('title')->get();

		$current_tab = session('current_tab', 'general');

		return view('account.customers.show', compact('customer','profile','countries','country_id','states','cities','modes','types','services','current_tab'));
	}

	public function postProfile($email)
	{
		$customer = $this->site->customers()->with('queries')->where('email', $email)->first();
		if ( !$customer ) 
		{
			abort(404);
		}

		$fields = [
			'country_id' => 'exists:countries,id',
			'territory_id' => 'exists:territories,id',
			'state_id' => 'exists:states,id',
			'city_id' => 'exists:cities,id',
			'district' => '',
			'zipcode' => '',
			'mode' => 'in:'.implode(',', \App\Property::getModes()),
			'type' => 'in:'.implode(',', array_keys(\App\Property::getTypeOptions())),
			'currency' => 'required|in:'.implode(',', array_keys(\App\Property::getCurrencyOptions())),
			'price_min' => 'numeric|min:0',
			'price_max' => 'numeric|min:'.intval($this->request->input('price_min')),
			'size_unit' => 'required|in:'.implode(',', array_keys(\App\Property::getSizeUnitOptions())),
			'size_min' => 'numeric|min:0',
			'size_max' => 'numeric|min:'.intval($this->request->input('size_min')),
			'rooms' => 'integer|min:0',
			'baths' => 'integer|min:0',
			'more_attributes' => 'array',
		];
		$validator = \Validator::make($this->request->all(), $fields);
		if ( $validator->fails() ) 
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		$profile = $customer->queries()->firstOrCreate([
			'enabled' => 1,
		]);
		if ( !$profile )
		{
			return redirect()->back()->withInput()->with('error',trans('general.messages.error'));
		}

		$data = [
			'more_attributes' => [],
		];
		foreach ($fields as $key => $value) 
		{
			$value = $this->request->input($key);

			switch ( $key )
			{
				// Nullable
				case 'country_id':
				case 'territory_id':
				case 'state_id':
				case 'city_id':
				case 'price_min':
				case 'price_max':
				case 'size_min':
				case 'size_max':
					$data[$key] = $value ? $value : null;
					break;
				default:
					$data[$key] = $value;
			}
		}
		$profile->update($data);

		return redirect()->action('Account\CustomersController@show', urlencode($customer->email))->with('current_tab', $this->request->input('current_tab'))->with('success', trans('general.messages.success.saved'));
	}

	public function getAddPropertyCustomer($slug)
	{
		$property = $this->site->properties()
						->whereTranslation('slug', $slug)
						->first();

		$customers = $this->site->customers()->orderBy('first_name')->orderBy('last_name')->orderBy('email')->get();

		return view('account.customers.add-property-customer', compact('property','customers'));
	}

	public function postAddPropertyCustomer($slug)
	{
		$property = $this->site->properties()
						->whereTranslation('slug', $slug)
						->first();
		if ( !$property )
		{
			return redirect()->back()->withInput()->with('error',trans('general.messages.error'));
		}

		$customer_id = $this->request->input('customer_id');

		if ( !$customer_id ) {
			$validator = \Validator::make($this->request->all(), $this->getRequiredFields());
			if ($validator->fails()) 
			{
				return redirect()->back()->withInput()->withErrors($validator);
			}

			$customer = $this->site->customers()->create([
				'first_name' => $this->request->input('first_name'),
				'last_name' => $this->request->input('last_name'),
				'email' => $this->request->input('email'),
				'phone' => $this->request->input('phone'),
				'locale' => $this->request->input('locale'),
				'created_by' => \Auth::user()->id,
			]);

			if ( !$customer )
			{
				return redirect()->back()->withInput()->with('error',trans('general.messages.error'));
			}

			$customer_id = $customer->id;
		}

		$customer = $this->site->customers()->find($customer_id);
		if ( !$customer )
		{
			return redirect()->back()->withInput()->with('error',trans('general.messages.error'));
		}

		// Associate
		if ( !$property->customers->contains( $customer_id ) )
		{
			$property->customers()->attach( $customer_id );
		}

		// Redirect back with current tab ?
		if ( $this->request->input('current_tab') )
		{
			return redirect()->back()->with('current_tab', $this->request->input('current_tab'))->with('success',trans('general.messages.success.saved'));
		}

		return redirect()->back()->with('success',trans('general.messages.success.saved'));
	}

	protected function deleteRemovePropertyCustomer($slug)
	{
		$property = $this->site->properties()
						->whereTranslation('slug', $slug)
						->first();
		if ( !$property )
		{
			return redirect()->back()->with('current_tab', $this->request->input('current_tab'))->with('error',trans('general.messages.error'));
		}

		$customer = $this->site->customers()->find( $this->request->input('customer_id') );

		if ( !$this->request->input('customer_id') || !$customer )
		{
			return redirect()->back()->with('current_tab', $this->request->input('current_tab'))->with('error',trans('general.messages.error'));
		}

		// Dissociate
		if ( $property->customers->contains( $customer->id ) )
		{
			$property->customers()->detach( $customer->id );
		}

		return redirect()->back()->with('current_tab', $this->request->input('current_tab'))->with('success',trans('general.messages.success.saved'));
	}

	protected function getRequiredFields($id=false)
	{
		$locales = array_keys( \App\Session\Site::get('locales_tabs') );

		$fields = [
			'first_name' => 'required',
			'last_name' => 'required',
			'email' => "required|email|unique:customers,email,".($id ? $id : 'NULL').",id,site_id,".$this->site->id,
			'phone' => 'required',
			'locale' => 'required|in:'.implode(',',$locales),
		];

		return $fields;		
	}
}
