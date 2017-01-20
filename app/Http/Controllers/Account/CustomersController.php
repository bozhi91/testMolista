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
		$price = $this->request->input('price');
		$mode = $this->request->input('mode');
		
		if($price || $mode) {
			$query = $this->site->customers()->whereIn('id', function($query) use($price, $mode){
			
				$subquery = $query->select('customer_id')
					->from('customers_queries');
			
				if($price){
					$subquery->where('price_min', '<=', $price);
					$subquery->where('price_max', '>=', $price);
				}
			
				if($mode){
					$subquery->where('mode', $mode);
				}
			
				return $subquery;
			})->with('queries');
		} else {
			$query = $this->site->customers()->with('queries');
		}
		
		$agent_id = !\Auth::user()->can('lead-view_all') ?
				\Auth::user()->id : $this->request->input('agent_id');
				
		if($agent_id) {
			$agent = \App\User::where('id', $agent_id)->firstOrFail();			
			$property_ids = $agent->properties()->pluck('id')->toArray();
						
			$customer_ids = !empty($property_ids) ? \DB::table('properties_customers')
				->whereIn('property_id', $property_ids)->pluck('customer_id') : [];
			
			$query->whereIn('id', $customer_ids);
		}
		
		if ( $this->site_user->hasRole('employee') )
		{
			$query->ofUser($this->site_user->id);
		}

		// Filter by name
		if ( $this->request->input('name') )
		{
			$query->withFullName( $this->request->input('name') );
		}

		// Filter by email and phone
		if ( $this->request->input('email') )
		{
			$query->where(function($q) {
				return $q->where('customers.email', 'like', "%{$this->request->input('email')}%")
						->orWhere('customers.phone', 'like', "%{$this->request->input('email')}%");
			});
		}

		//Filter by active
		if($this->request->input('active') || $this->request->input('active') === '0') {
			$query->where('active', $this->request->input('active'));
		}

		//Filter by origin
		if($this->request->input('origin')) {
			$query->where('origin', $this->request->input('origin'));
		}

		//Filter by created at
		if($this->request->input('created_at')) {
			$query->where(\DB::raw("DATE_FORMAT(created_at, '%d/%m/%Y')"), 'like', "%{$this->request->input('created_at')}%");
		}

		//Filter by properties
		$propertiesQuery = \DB::raw('(SELECT COUNT(*) FROM properties_customers WHERE properties_customers.customer_id = customers.id)');
		if($this->request->input('properties')) {
			$query->where($propertiesQuery, $this->request->input('properties'));
		}

		$order = $this->request->input('order');
		switch ( $this->request->input('orderby') )
		{
			case 'name':
				$query->orderBy('first_name', $order);
				$query->orderBy('last_name', $order);
				break;
			case 'email':
				$query->orderBy('email', $order);
				break;
			case 'origin':
				$query->orderBy('origin', $order);
				break;
			case 'properties':
				$query->orderBy($propertiesQuery, $order);
				break;
			case 'matches':
				$query->orderBy('matches_count', $order);
				break;
			case 'status':
				$query->orderBy('active', $order);
				break;
			case 'creation':
			default:
				$query->orderBy('created_at', $order ? $order : 'desc');
				break;
		}

		if ( $this->request->input('csv') )
		{
			return $this->exportCsv($query);
		}

		$customers = $query->paginate( $this->request->input('limit', \Config::get('app.pagination_perpage', 10)) );

		if ( $customers->count() > 0 )
		{
			$ids = $customers->pluck('id')->all();
			$stats = $this->site->ticket_adm->getCustomersStats($ids);
		}

		$this->set_go_back_link();

		$agents = $this->site->users()->withRole('employee')->with('properties')->lists('name', 'id')->toArray();
		
		return view('account.customers.index', compact('customers', 'stats', 'agents'));
	}

	public function exportCsv($query)
	{
		$columns = [
			'full_name' => trans('account/customers.name'),
			'email' => trans('account/customers.email'),
			'total_properties' => trans('account/customers.properties'),
			'total_matches' => trans('account/customers.matches'),
		];

		$csv = \League\Csv\Writer::createFromFileObject(new \SplTempFileObject());
		$csv->setDelimiter(';');

		// Headers
		$csv->insertOne( array_values($columns) );

		// Lines
		foreach ($query->limit(99999)->get() as $customer)
		{
			$data = [];

			foreach ($columns as $key => $value)
			{
				switch ($key)
				{
					case 'total_properties':
						$data[] = $customer->properties->count();
						break;
					case 'total_matches':
						$data[] = $customer->possible_matches->count();
						break;
					default:
						$data[] = $customer->$key;
				}
			}

			$csv->insertOne( $data );
		}

		$csv->output('leads_'.date('Ymd').'.csv');
		exit;
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
			'dni' => $this->request->input('dni'),
			'created_by' => $this->site_user->id,
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
		// If $email is integer,  redirect
		if ( preg_match('#^[0-9]+$#', $email) )
		{
			$customer = $this->site->customers()->findOrFail($email);
			return redirect()->action('Account\CustomersController@show', urlencode($customer->email));
		}

		$query = $this->site->customers()
					->with('queries')
					->with([ 'properties' => function($query){
						$query->with('calendars');
					}])
					->with('properties_discards')
					->where('email', $email);

		if ( $this->site_user->hasRole('employee') )
		{
			$query->ofUser($this->site_user->id);
		}

		$customer = $query->first();
		
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

		$districts = $this->site->districts()->lists('name', 'id')->all();
		
		return view('account.customers.show', compact('customer','profile','countries','country_id','states','cities','modes','types','services','current_tab', 'districts'));
	}

	public function destroy($email)
	{
		$customer = $this->site->customers()->where('email', $email)->first();
		if ( !$customer )
		{
			return redirect()->back()->with('error',trans('general.messages.error'));
		}

		// Delete customer (molista & tickets)
		if ( $this->site->ticket_adm->dissociateContact($customer) )
		{
			$customer->delete();
			return redirect()->action('Account\CustomersController@index')->with('success',trans('account/customers.message.deleted'));
		}

		return redirect()->back()->with('error',trans('general.messages.error'));
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

		$this->saveCustomerDistricts($customer, $this->request->input('district_id'));
		$this->saveCustomerCities($customer, $this->request->input('city_id'));
		
		return redirect()->action('Account\CustomersController@show', urlencode($customer->email))->with('current_tab', $this->request->input('current_tab'))->with('success', trans('general.messages.success.saved'));
	}

	/**
	 * @param Customer $customer
	 * @param array $district_ids
	 */
	private function saveCustomerDistricts($customer, $district_ids){		
		\App\Models\Site\CustomerDistrict::where('customer_id', $customer->id)->delete();
		
		if(empty($district_ids)){
			return;
		}
		
		$data = [];
		foreach ($district_ids as $districtId) {
			if ($districtId != 0) {
				$data[] = ['customer_id' => $customer->id, 'district_id' => $districtId];
			}
		}
		\App\Models\Site\CustomerDistrict::insert($data);
	}
	
	/**
	 * @param Customer $customer
	 * @param array $city_ids
	 */
	private function saveCustomerCities($customer, $city_ids){
		\App\Models\Site\CustomerCity::where('customer_id', $customer->id)->delete();
		
		if(empty($city_ids)){
			return;
		}
				
		$data = [];
		foreach ($city_ids as $cityId) {
			if ($cityId != 0) {
				$data[] = ['customer_id' => $customer->id, 'city_id' => $cityId];
			}
		}
		\App\Models\Site\CustomerCity::insert($data);
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
				'created_by' => $this->site_user->id,
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

	public function deleteRemovePropertyCustomer($slug)
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

		// Discard
		if ( !$customer->properties_discards->contains( $property->id ) )
		{
			$customer->properties_discards()->attach( $property->id );
		}

		return redirect()->back()->with('current_tab', $this->request->input('current_tab'))->with('success',trans('general.messages.success.saved'));
	}

	public function putUndiscardPropertyCustomer($slug)
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

		// Undiscard
		if ( $customer->properties_discards->contains( $property->id ) )
		{
			$customer->properties_discards()->detach( $property->id );
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

	public function postComment($slug)
	{
		$customer = $this->site->customers()->where('email', $slug)->first();
		if ( !$customer )
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		$customer->update([
			'comment' => $this->request->input('comment')
		]);

		return redirect()->back()->with('success', trans('general.messages.success.saved'));
	}

	public function getChangeStatus($email)
	{
		// If $email is integer,  redirect
		if ( preg_match('#^[0-9]+$#', $email) )
		{
			$customer = $this->site->customers()->findOrFail($email);
			return redirect()->action('Account\CustomersController@show', urlencode($customer->email));
		}

		$query = $this->site->customers()
					->with('queries')
					->with([ 'properties' => function($query){
						$query->with('calendars');
					}])
					->with('properties_discards')
					->where('email', $email);

		if ( $this->site_user->hasRole('employee') )
		{
			$query->ofUser($this->site_user->id);
		}

		$customer = $query->first();

		if ( !$customer )
		{
			return [ 'error'=>1 ];
		}

		$customer->active = !$customer->active;
		$customer->save();

		return [
			'success' => 1,
			'active' => $customer->active
		];
	}

	public function postGeneral($slug)
	{
		$customer = $this->site->customers()->where('email', $slug)->first();
		if ( !$customer )
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		$customer->update([
			'alert_config' => $this->request->input('alerts')
		]);

		return redirect()->back()->with('success', trans('general.messages.success.saved'));
	}

}
