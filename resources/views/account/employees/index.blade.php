@extends('layouts.account')

@section('account_content')

	<div id="admin-employees" class="row">
		<div class="col-xs-12">

	        @include('common.messages', [ 'dismissible'=>true ])

			@if ( Auth::user()->can('employee-create') )
				<div class="pull-right">
					<a href="{{ action('Account\EmployeesController@create') }}" class="btn btn-primary">{{ Lang::get('account/employees.button.new') }}</a>
				</div>
			@endif

			<h1 class="page-title">{{ Lang::get('account/employees.h1') }}</h1>

			<div class="search-filters">
				@if ( !empty($clean_filters) )
					<a href="?limit={{ Input::get('limit') }}" class="text-bold pull-right">{{ Lang::get('general.filters.clean') }}</a>
				@endif
				<h2>{{ Lang::get('general.filters') }}</h2>
				{!! Form::open([ 'method'=>'GET', 'class'=>'form-inline', 'id'=>'filters-form' ]) !!}
					<div class="form-group">
						{!! Form::label('name', Lang::get('account/customers.name'), [ 'class'=>'sr-only' ]) !!}
						{!! Form::text('name', Input::get('name'), [ 'class'=>'form-control', 'placeholder'=>Lang::get('account/customers.name') ]) !!}
					</div>
					<div class="form-group">
						{!! Form::label('email', Lang::get('account/customers.email'), [ 'class'=>'sr-only' ]) !!}
						{!! Form::text('email', Input::get('email'), [ 'class'=>'form-control', 'placeholder'=>Lang::get('account/customers.email') ]) !!}
					</div>
					{!! Form::submit(Lang::get('general.view'), [ 'class'=>'btn btn-default' ]) !!}
				{!! Form::close() !!}
			</div>
			
			@if ( count($employees) < 1)
				<div class="alert alert-info">{{ Lang::get('account/employees.empty') }}</div>
			@else
				<table class="table table-striped">
					<thead>
						<tr>
							{!! drawSortableHeaders(url()->full(), [
								'name' => [ 'title' => Lang::get('account/employees.name') ],
								'email' => [ 'title' => Lang::get('account/employees.email') ],
								'properties' => [ 'title' => Lang::get('account/employees.properties'), 'class'=>'text-center', 'sortable'=>false ],
								'tickets' => [ 'title' => Lang::get('account/employees.tickets'), 'sortable'=>false, 'class'=>'text-center text-nowrap' ],
								'leads' => [ 'title' => Lang::get('account/employees.leads'), 'class'=>'text-center', 'sortable'=>false ],
								'action' => [ 'title' => '', 'sortable'=>false ],
							]) !!}
						</tr>
					</thead>
					<tbody>
						@foreach ($employees as $employee)
							<tr>
								<td class="text-nowrap">
									<img src="{{ $employee->image_url }}" class="employee-image-thumb" />
									{{ $employee->name }}
								</td>
								<td>{{ $employee->email }}</td>
								<td class="text-center">{{ number_format($employee->properties->where('site_id', $site_setup['site_id'])->count(), 0, ',', '.') }}</td>
								<td class="text-center">{{ @number_format(intval( $stats[$employee->ticket_user_id]->tickets->open ), 0, ',', '.') }}</td>
								<td class="text-center">{{ @number_format(intval( $stats[$employee->ticket_user_id]->contacts->open ), 0, ',', '.') }}</td>
								<td class="text-right text-nowrap">
									{!! Form::open([ 'method'=>'DELETE', 'class'=>'delete-form', 'action'=>['Account\EmployeesController@destroy', urlencode($employee->email)] ]) !!}
										@if ( Auth::user()->can('employee-edit') )
											<a href="{{ action('Account\EmployeesController@edit', urlencode($employee->email)) }}" class="btn btn-primary btn-xs">{{ Lang::get('general.edit') }}</a>
										@endif
										@if ( Auth::user()->can('employee-delete') )
											<button type="submit" class="btn btn-danger btn-xs">{{ Lang::get('account/employees.button.dissociate') }}</button>
										@endif
									{!! Form::close() !!}
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
                {!! drawPagination($employees, Input::only('limit','name','email'), action('Account\EmployeesController@index', [ 'csv'=>1 ])) !!}
			@endif

		</div>
	</div>

	<script type="text/javascript">
		ready_callbacks.push(function() {
			var cont = $('#admin-employees');

			/*
			cont.find('form.delete-form').each(function(){
				$(this).validate({
					submitHandler: function(f) {
						SITECOMMON.confirm("{{ print_js_string( Lang::get('account/employees.delete') ) }}", function (e) {
							if (e) {
								LOADING.show();
								f.submit();
							}
						});
					}
				});
			});
			*/

		});
	</script>

@endsection