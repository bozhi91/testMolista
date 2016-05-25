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
		$customer = $this->site->customers()->with('queries')->where('email', $email)->first();
		if ( !$customer )
		{
			abort(404);
		}

		$profile = $customer->current_query;

		$countries = \App\Models\Geography\Country::withTranslations()->enabled()->orderBy('name')->lists('name','id')->all();
		if ( $country_id = @$profile->country_id ? $profile->country_id : \App\Models\Geography\Country::where('code','ES')->value('id') )
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

		return view('account.customers.show', compact('customer','profile','countries','country_id','states','cities','modes','types','services'));
	}

	public function postProfile($email)
	{
		$customer = $this->site->customers()->with('queries')->where('email', $email)->first();
		if ( !$customer ) 
		{
			return [ 'error'=>'Customer not found' ];
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
			'price_max' => 'numeric|min:'.intval($this->request->get('price_min')),
			'size_unit' => 'required|in:'.implode(',', array_keys(\App\Property::getSizeUnitOptions())),
			'size_min' => 'numeric|min:0',
			'size_max' => 'numeric|min:'.intval($this->request->get('size_min')),
			'rooms' => 'integer|min:0',
			'baths' => 'integer|min:0',
			'newly_build' => 'boolean',
			'second_hand' => 'boolean',
			'services' => 'array',
		];
		$validator = \Validator::make($this->request->all(), $fields);
		if ( $validator->fails() ) 
		{
			return [ 'error'=>1, 'errors'=>$validator->errors() ];
		}

		$profile = $customer->queries()->firstOrCreate([
			'enabled' => 1,
		]);
		if ( !$profile )
		{
			return [ 'error'=>'Error creating profile' ];
		}

		$data = [
			'more_attributes' => [],
		];
		foreach ($fields as $key => $value) 
		{
			$value = $this->request->get($key);

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
				// Varchar
				case 'mode':
				case 'type':
				case 'rooms':
				case 'baths':
				case 'district':
				case 'zipcode':
				case 'currency':
				case 'size_unit':
					$data[$key] = $value;
					break;
				// Other
				default:
					$data['more_attributes'][$key] = $value;
			}
		}
		$profile->update($data);

		return [ 'success'=>1 ];
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
