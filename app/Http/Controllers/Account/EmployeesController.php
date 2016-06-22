<?php

namespace App\Http\Controllers\Account;

use Illuminate\Http\Request;

use App\Http\Requests;

class EmployeesController extends \App\Http\Controllers\AccountController
{
	public function __initialize()
	{
		parent::__initialize();
		$this->middleware([ 'permission:employee-view' ], [ 'only' => [ 'index' ] ]);
		$this->middleware([ 'permission:employee-create' ], [ 'only' => [ 'create','store' ] ]);
		$this->middleware([ 'permission:employee-edit' ], [ 'only' => [ 'edit','update','getAssociate','postAssociate','getDissociate' ] ]);
		$this->middleware([ 'permission:employee-delete' ], [ 'only' => [ 'destroy' ] ]);
		$this->middleware([ 'permission:property-edit' ], [ 'only' => [ 'getAssociate','postAssociate','getDissociate' ] ]);

		\View::share('submenu_section', 'employees');
	}

	public function index()
	{
		$query = $this->site->users()
					->withRole('employee')
					->with('properties');

		// Filter by name
		if ( $this->request->get('name') )
		{
			$query->where('name', 'like', "%{$this->request->get('name')}%");
		}

		// Filter by email
		if ( $this->request->get('email') )
		{
			$query->where('email', 'like', "%{$this->request->get('email')}%");
		}

		if ( $this->request->get('csv') )
		{
			return $this->exportCsv($query);
		}

		$employees = $query->orderBy('name')->paginate( $this->request->get('limit', \Config::get('app.pagination_perpage', 10)) );

		if ( $employees->count() > 0 )
		{
			$stats = $this->site->ticket_adm->getUsersStats( array_filter($employees->pluck('ticket_user_id')->all()) );
		}

		$this->set_go_back_link();

		return view('account.employees.index', compact('employees','stats'));
	}

	public function exportCsv($query)
	{
		$employees = $query->with([ 'properties' => function($query){
			$query->ofSite( $this->site->id );
		}])->limit(9999)->get();

		if ( $employees->count() > 0 )
		{
			$stats = $this->site->ticket_adm->getUsersStats( array_filter($employees->pluck('ticket_user_id')->all()) );
		}

		$columns = [
			'name' => trans('account/employees.name'),
			'email' => trans('account/employees.email'),
			'properties' => trans('account/employees.properties'),
			'tickets' => trans('account/employees.tickets'),
			'leads' => trans('account/employees.leads'),
		];

		$csv = \League\Csv\Writer::createFromFileObject(new \SplTempFileObject());
		$csv->setDelimiter(';');

		// Headers
		$csv->insertOne( array_values($columns) );

		// Lines
		foreach ($employees as $employee)
		{
			$data = [];

			foreach ($columns as $key => $value) 
			{
				switch ($key) 
				{
					case 'properties':
						$data[] = number_format($employee->properties->count(), 0, ',', '.');
						break;
					case 'tickets':
						$data[] = @number_format(intval( $stats[$employee->ticket_user_id]->tickets->open ), 0, ',', '.');
						break;
					case 'leads':
						$data[] = @number_format(intval( $stats[$employee->ticket_user_id]->contacts->open ), 0, ',', '.');
						break;
					default:
						$data[] = $employee->$key;
				}
			}

			$csv->insertOne( $data );
		}

		$csv->output('agents_'.date('Ymd').'.csv');
		exit;
	}
	public function create()
	{
		return view('account.employees.create');
	}

	public function store()
	{
		$validator = \Validator::make($this->request->all(), [
			'name' => 'required|max:255',
			'email' => 'required|email|max:255',
			'password' => 'required|min:6',
			'locale' => 'required|string|in:'.implode(',',\LaravelLocalization::getSupportedLanguagesKeys()),
		]);
		if ($validator->fails()) 
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		// Check email
		if ( $exists = \App\User::where('email', $this->request->get('email'))->first() )
		{
			if ( $exists->hasRole('employee') )
			{
				return redirect()->action('Account\EmployeesController@getAssociate', urlencode($exists->email))->withInput();
			}
			return redirect()->back()->withInput()->with('error', trans('account.employees.email.used'));
		}

		// Create user associated to this site
        $employee = $this->site->users()->create([
            'name' => sanitize( $this->request->get('name') ),
            'email' => sanitize( $this->request->get('email'), 'email'),
            'locale' => $this->request->get('locale'),
            'password' => bcrypt($this->request->get('password')),
        ]);

		if ( !$employee )
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		// Attach employee role
		$employee->roles()->attach( \App\Models\Role::where('name','employee')->value('id') );

		// Associate in ticketing system
		$this->site->ticket_adm->associateUsers([ $employee ]);

		return redirect()->action('Account\EmployeesController@edit', urlencode($employee->email))->with('success', trans('account/employees.message.saved'));
	}

	public function edit($email)
	{
		$employee = $this->site->users()->withRole('employee')->where('email', $email)->withPivot('can_create','can_edit','can_delete')->first();
		if ( !$employee )
		{
			abort(404);
		}

		$properties = $employee->properties()->ofSite( $this->site->id )->withTranslations()->get();

		return view('account.employees.edit', compact('employee','properties'));
	}

	public function update($email)
	{
		$employee = $this->site->users()->withRole('employee')->where('email', $email)->withPivot('can_create','can_edit','can_delete')->first();
		if ( !$employee )
		{
			return redirect()->action('Account\EmployeesController@edit', urlencode($email))->with('error', trans('general.messages.error'));
		}

		// Change pivot values
		foreach (['create','edit','delete'] as $permission)
		{	
			$key = "can_{$permission}";
			$employee->pivot->$key = $this->request->input("permissions.{$key}") ? 1 : 0;
		}
		$employee->pivot->save();

		return redirect()->action('Account\EmployeesController@edit', urlencode($employee->email))->with('success', trans('account/employees.message.saved'));
	}

	public function destroy($email)
	{
		$employee = $this->site->users()->withRole('employee')->where('email', $email)->first();
		if ( !$employee )
		{
			abort(404);
		}

		// Dissociate properties
		foreach ($employee->properties()->ofSite( $this->site->id )->get() as $property)
		{
			$property->users()->detach( $employee->id );
		}

		// Dissociate from site
		$employee->sites()->detach( $this->site->id );

		// Dissociate in ticketing system
		$this->site->ticket_adm->dissociateUsers([ $employee ]);

		return redirect()->action('Account\EmployeesController@index')->with('success', trans('account/employees.deleted'));
	}

	public function getTickets($email)
	{
		$employee = \App\User::withRole('employee')->where('email', $email)->first();
		if ( $employee && $employee->ticket_user_id )
		{
			$tickets = $this->site->ticket_adm->getTickets([
				'user_id' => $employee->ticket_user_id,
				'status' => [ 'open', 'waiting' ],
				'page' => $this->request->get('page',1),
				'limit' => $this->request->get('limit', \Config::get('app.pagination_perpage', 10)),
				'orderby' => $this->request->get('orderby'),
				'order' => $this->request->get('order'),
			]);
		}

		$pagination_url = url()->full(); //action('Account\EmployeesController@getTickets', urlencode($email));

		$ticket_list_target = '_blank';

		return view('account.tickets.list', compact('pagination_url','tickets','ticket_list_target'));
	}

	public function getAssociate($email)
	{
		$employee = \App\User::withRole('employee')->where('email', $email)->first();
		if ( !$employee )
		{
			abort(404);
		}

		return view('account.employees.associate', compact('employee'));
	}
	public function postAssociate($email)
	{
		$employee = \App\User::withRole('employee')->where('email', $email)->first();
		if ( $employee && !$this->site->users->contains( $employee->id ) )
		{
			$this->site->users()->attach($employee->id);
		}

		// Associate in ticketing system
		$this->site->ticket_adm->associateUsers([ $employee ]);

		return redirect()->action('Account\EmployeesController@edit', urlencode($employee->email))->with('success', trans('account/employees.message.associated'));
	}

	public function getDissociate($user_id,$property_id)
	{
		$property = $this->site->properties()->find($property_id);
		$employee = $this->site->users()->find($user_id);

		if ( !$property || !$employee )
		{
			return [ 'error'=>1 ];
		}

		$property->users()->detach($employee->id);

		return [ 'success'=>1 ];
	}

}
