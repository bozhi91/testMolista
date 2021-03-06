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
		$this->middleware([ 'permission:employee-create', 'employee.permission:create' ], [ 'only' => [ 'create','store' ] ]);
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
		if ( $this->request->input('name') )
		{
			$query->where('name', 'like', "%{$this->request->input('name')}%");
		}

		// Filter by email
		if ( $this->request->input('email') )
		{
			$query->where('email', 'like', "%{$this->request->input('email')}%");
		}

		$order = $this->request->input('order');		
		switch ($this->request->input('orderby')){
			case 'name': 
				$query->orderBy('name', $order); 
				break;
			case 'email': 
				$query->orderBy('email', $order); 
				break;
		}
		
		if ( $this->request->input('csv') )
		{
			return $this->exportCsv($query);
		}

		$employees = $query->paginate( $this->request->input('limit', \Config::get('app.pagination_perpage', 10)) );

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
		$fields = \App\User::getFields();

		// Allow email repeated
		$fields['email'] = 'required|email';

		$validator = \Validator::make($this->request->all(), $fields);
		if ( $validator->fails() )
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		// Check if email exists
		if ( $exists = \App\User::where('email', $this->request->get('email'))->first() )
		{
			// Check if has employee role
			if ( $exists->hasRole('employee') )
			{
				return redirect()->action('Account\EmployeesController@getAssociate', urlencode($exists->email))->withInput();
			}
			// Return custom error
			return redirect()->back()->withInput()->with('error', trans('account.employees.email.used'));
		}

		$employee = \App\User::saveModel($this->request->all());
		if ( !$employee )
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		// Attach employee role
		$employee->roles()->attach( \App\Models\Role::where('name','employee')->value('id') );

		// Associate to site
		$this->site->users()->attach($employee->id);

		// Associate in ticketing system
		$this->site->ticket_adm->associateUsers([ $employee ]);

		return redirect()->action('Account\EmployeesController@edit', urlencode($employee->email))->with('success', trans('account/employees.message.saved'));
	}

	public function edit($email)
	{
		$employee = $this->site->users()->withRole('employee')->where('email', $email)->withPivot('can_create','can_edit','can_delete','can_view_all','can_edit_all','can_delete_all')->first();
		if ( !$employee )
		{
			abort(404);
		}
		
		$customers = $employee->getCustomers()
				->paginate( $this->request->input('limit', \Config::get('app.pagination_perpage', 10)) );
				
		$assigned_properties = $employee->properties()->ofSite( $this->site->id )->pluck('id')->toArray();
		
		$properties = $this->site->properties()
				->paginate( $this->request->input('limit', \Config::get('app.pagination_perpage', 10)) );
		
	
		
		return view('account.employees.edit', compact('employee','properties', 'customers', 'assigned_properties'));
	}

	
	public function getChangeRelation($email, $property_id){
		$property = $this->site->properties()->find($property_id);
		$employee = $this->site->users()->where('email', $email)->first();

		if ( !$property || !$employee )
		{
			return [ 'error'=>1 ];
		}

		$user_assigned = $property->users()->where('id', $employee->id)->first();
		
		if($user_assigned) {
			$property->users()->detach($employee->id);
			return [ 'success'=>1, 'active' => false];
		} else {
			$property->users()->attach($employee->id);
			return [ 'success'=>1, 'active' => true];
		}
	}
	
	public function update($email)
	{
		$employee = $this->site->users()->withRole('employee')->where('email', $email)->withPivot('can_create','can_edit','can_delete','can_view_all','can_edit_all','can_delete_all')->first();
		if ( !$employee )
		{
			return redirect()->action('Account\EmployeesController@edit', urlencode($email))->with('error', trans('general.messages.error'));
		}

		// Change pivot values
		foreach (['create','edit','delete','view_all','edit_all','delete_all'] as $permission)
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

		if ( !$this->request->input('confirm') )
		{
			$employees = $this->site->users()->where('id', '!=', $employee->id)->orderby('name')->lists('name','id')->all();
			return view('account.employees.destroy', compact('employee','employees'));
		}

		// Confirm ID to reasign
		$reassignee = false;
		if ( $reassign_to = $this->request->input('reassign_to') )
		{
			$reassignee = $this->site->users()->findOrFail( $reassign_to );
		}

		// Properties
		foreach ($employee->properties()->ofSite( $this->site->id )->get() as $property)
		{
			// Reasign?
			if ( $reassign_to && !$property->users->contains( $reassign_to ) )
			{
				$property->users()->attach( $reassign_to );
			}
			// Dissociate
			$property->users()->detach( $employee->id );
		}

		// Events
		foreach ($employee->calendars()->ofSite( $this->site->id )->get() as $event)
		{
			// Reasign?
			if ( $reassign_to && !$event->users->contains( $reassign_to ) )
			{
				$event->users()->attach( $reassign_to );
			}
			// Dissociate
			$event->users()->detach( $employee->id );
			// If no users, cancel event
			if ( $event->users()->count() < 1 )
			{
				\App\Models\Calendar::sendNotification('cancel',$event);
				$event->delete();
			}
		}

		// Dissociate in ticketing system
		$this->site->ticket_adm->dissociateUsers([ $employee ], $reassignee);

		// Dissociate from site
		$this->site->users()->detach( $employee->id );

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
				'page' => $this->request->input('page',1),
				'limit' => $this->request->input('limit', \Config::get('app.pagination_perpage', 10)),
				'orderby' => $this->request->input('orderby'),
				'order' => $this->request->input('order'),
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
