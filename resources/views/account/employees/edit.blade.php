@extends('layouts.account')

@section('account_content')

	<style type="text/css">
		#account-visits-ajax-tab .column-agent { display: none; }
	</style>

	<div id="admin-employees">

		@include('common.messages', [ 'dismissible'=>true ])

		<h1 class="page-title">{{ Lang::get('account/employees.show.title', [
			'name' => $employee->name,
			'email' => $employee->email,
		]) }}</h1>

		{!! Form::model($employee, [ 'method'=>'PATCH', 'action'=>['Account\EmployeesController@update', urlencode($employee->email)], 'id'=>'employee-form' ]) !!}

			<div class="custom-tabs">

				<ul class="nav nav-tabs main-tabs" role="tablist">
					<li role="presentation" class="active"><a href="#tab-properties" aria-controls="tab-properties" role="tab" data-toggle="tab">{{ Lang::get('account/employees.show.tab.properties') }}</a></li>
					<li role="presentation"><a href="#tab-permissions" aria-controls="tab-permissions" role="tab" data-toggle="tab">{{ Lang::get('account/employees.show.tab.permissions') }}</a></li>
					<li role="presentation"><a href="#tab-tickets" aria-controls="tab-tickets" role="tab" data-toggle="tab">{{ Lang::get('account/employees.tickets') }}</a></li>
					<li role="presentation"><a href="#tab-visits" aria-controls="tab-visits" role="tab" data-toggle="tab">{{ Lang::get('account/visits.title') }}</a></li>
					<li role="presentation"><a href="#tab-leads" aria-controls="tab-leads" role="tab" data-toggle="tab">{{ Lang::get('account/employees.leads') }}</a></li>
				</ul>

				<div class="tab-content">

					<div role="tabpanel" class="tab-pane tab-main active" id="tab-properties">
						@if ( $employee->pivot->can_view_all )
							<div style="font-weight: bold; padding-top: 10px;">{{ Lang::get('account/employees.show.tab.permissions.view_all.warning') }}</div>
							<hr />
						@endif
						@if ( $employee->pivot->can_edit_all )
							<div style="font-weight: bold; padding-top: 10px;">{{ Lang::get('account/employees.show.tab.permissions.edit_all.warning') }}</div>
							<hr />
						@endif
						@if ( $employee->pivot->can_delete_all )
							<div style="font-weight: bold; padding-top: 10px;">{{ Lang::get('account/employees.show.tab.permissions.delete_all.warning') }}</div>
							<hr />
						@endif

						@if ( $properties->count() < 1)
							<div class="alert alert-info properties-empty">
								{{ Lang::get('account/employees.show.tab.properties.empty') }}
							</div>
						@else
							<table class="table table-striped">
							<thead>
								<tr>
									{!! drawSortableHeaders(url()->full(), [
										'assigned' => [ 'title' => Lang::get('account/employees.show.tab.properties.assigned'), 'sortable'=>false, 'class'=>'text-center' ],
										'reference' => [ 'title' => Lang::get('account/properties.ref'), 'sortable'=>false, ],
										'address' => [ 'title' => Lang::get('account/properties.column.address'), 'sortable'=>false, ],
										'price' => [ 'title' => Lang::get('account/properties.column.price'), 'sortable'=>false, ],
										'action' => [ 'title' => '', 'sortable'=>false ],
									]) !!}
								</tr>
							</thead>
							<tbody>
								@foreach ($properties as $property)
								<tr>
									<td class="text-center">
										<a href="#" data-url="{{ action('Account\EmployeesController@getChangeRelation', [
											'email' => $employee->email, 'property_id' => $property->id]) }}" class="change-status-trigger">
											<span class="glyphicon glyphicon-{{ in_array($property->id, $assigned_properties) ? 'ok' : 'remove' }}" aria-hidden="true"></span>
										</a>
									</td>
									<td>{{ $property->ref }}</td>
									<td>{!! implode( [
										$property->address,
										@implode(' / ', array_filter([ $property->city->name, $property->state->name ]))
										], '<br>') !!}</td>
									<td>{{ $property->price }}</td>
									<td class="text-right text-nowrap">
										<a href="{{ action('Account\PropertiesController@show', $property->slug) }}" class="btn btn-primary btn-xs">{{ Lang::get('general.view') }}</a>
									</td>
								</tr>
								@endforeach
							</tbody>
							</table>
							{!! drawPagination($properties, Input::except('page')) !!}
						@endif
					</div>

					<div role="tabpanel" class="tab-pane tab-main" id="tab-permissions">
						<ul class="list-unstyled">
							<li>
								<div class="form-group">
									<div class="checkbox">
										<label>
											{!! Form::checkbox('permissions[can_view_all]', 1, $employee->pivot->can_view_all) !!}
											{{ Lang::get('account/employees.show.tab.permissions.view_all') }}
										</label>
									</div>
								</div>
							</li>
							<li>
								<div class="form-group">
									<div class="checkbox">
										<label>
											{!! Form::checkbox('permissions[can_edit_all]', 1, $employee->pivot->can_edit_all) !!}
											{{ Lang::get('account/employees.show.tab.permissions.edit_all') }}
										</label>
									</div>
								</div>
							</li>
							<li>
								<div class="form-group">
									<div class="checkbox">
										<label>
											{!! Form::checkbox('permissions[can_delete_all]', 1, $employee->pivot->can_delete_all) !!}
											{{ Lang::get('account/employees.show.tab.permissions.delete_all') }}
										</label>
									</div>
								</div>
							</li>
							<li>
								<div class="form-group">
									<div class="checkbox">
										<label>
											{!! Form::checkbox('permissions[can_create]', 1, $employee->pivot->can_create) !!}
											{{ Lang::get('account/employees.show.tab.permissions.create') }}
										</label>
									</div>
								</div>
							</li>
							<li>
								<div class="form-group">
									<div class="checkbox">
										<label>
											{!! Form::checkbox('permissions[can_edit]', 1, $employee->pivot->can_edit) !!}
											{{ Lang::get('account/employees.show.tab.permissions.edit') }}
										</label>
									</div>
								</div>
							</li>
							<li>
								<div class="form-group">
									<div class="checkbox">
										<label>
											{!! Form::checkbox('permissions[can_delete]', 1, $employee->pivot->can_delete) !!}
											{{ Lang::get('account/employees.show.tab.permissions.delete') }}
										</label>
									</div>
								</div>
							</li>
						</ul>
					</div>

					<div role="tabpanel" class="tab-pane tab-main" id="tab-tickets" data-url="{{ action('Account\EmployeesController@getTickets', urlencode($employee->email)) }}"></div>

					<div role="tabpanel" class="tab-pane tab-main" id="tab-visits">
						@include('account.visits.ajax-tab', [
							'visits_init' => true,
						])
					</div>

					<div role="tabpanel" class="tab-pane tab-main" id="tab-leads">
						@if ( count($customers->count()) < 1)
							<div class="alert alert-info">{{ Lang::get('account/customers.empty') }}</div>
						@else
							<table class="table table-striped">
								<thead>
								<tr>
									{!! drawSortableHeaders(url()->full(), [
										'name' => [ 'title' => Lang::get('account/customers.name'), 'sortable'=>false, ],
										'email' => [ 'title' => Lang::get('account/customers.email'), 'sortable'=>false, ],
										'origin' => [ 'title' => Lang::get('account/customers.origin'), 'sortable'=>false, ],
										'properties' => [ 'title' => Lang::get('account/customers.properties'), 'sortable'=>false, 'class'=>'text-center', ],
										'matches' => [ 'title' => Lang::get('account/customers.matches'), 'sortable'=>false, 'class'=>'text-center', ],
										'action' => [ 'title' => '', 'sortable'=>false, 'class'=>'text-right text-nowrap', ],
									]) !!}
								</tr>
								</thead>
								<tbody>
								@foreach ($customers as $customer)
									<tr>
										<td>{{ $customer->full_name }}</td>
										<td>{{ $customer->email }}</td>
										<td style="text-transform: capitalize;">{{ $customer->origin }}</td>
										<td class="text-center">{{ number_format($customer->properties->count(), 0, ',', '.') }}</td>
										<td class="text-center">{{ number_format($customer->possible_matches->count(), 0, ',', '.') }}</td>
										<td class="text-right text-nowrap">
											<a href="{{ action('Account\CustomersController@show', urlencode($customer->email)) }}"
											   class="btn btn-primary btn-xs">{{ Lang::get('general.view') }}</a>
										</td>
									</tr>
								@endforeach
								</tbody>
							</table>
							{!! drawPagination($customers, Input::except('page'), url()->full()) !!}
						@endif
					</div>

				</div>

			</div>

			<br />

			<div class="text-right">
				{!! print_goback_button( Lang::get('general.back'), [ 'class'=>'btn btn-default' ]) !!}
				{!! Form::submit( Lang::get('general.save.changes'), [ 'class'=>'btn btn-primary']) !!}
			</div>

		{!! Form::close() !!}

	</div>

	<script type="text/javascript">
		function reloadTickets() {
			TICKETS.reload();
		}

		ready_callbacks.push(function(){
			var cont = $('#admin-employees');
			var form = $('#employee-form');

			$('#tab-leads').pjax('.pagination a');

			form.validate({
				ignore: '',
				errorPlacement: function(error, element) {
					element.closest('.error-container').append(error);
				},
				invalidHandler: function(e, validator){
					if ( validator.errorList.length ) {
						var el = $(validator.errorList[0].element);
						form.find('.main-tabs a[href="#' + el.closest(".tab-main").attr('id') + '"]').tab('show');
						if ( el.closest('.tab-locale').length ) {
							form.find('.locale-tabs a[href="#' + el.closest(".tab-locale").attr('id') + '"]').tab('show');
						}
					}
				},
				submitHandler: function(f) {
					LOADING.show();
					f.submit();
				}
			});

			cont.on('click', '.change-status-trigger', function(e){
				e.preventDefault();

				LOADING.show();

				var el = $(this);

				$.ajax({
					dataType: 'json',
					url: el.data().url,
					success: function(data) {
						LOADING.hide();
						if (data.success) {
							if (data.active) {
								el.find('.glyphicon').removeClass('glyphicon-remove').addClass('glyphicon-ok');
							} else {
								el.find('.glyphicon').removeClass('glyphicon-ok').addClass('glyphicon-remove');
							}
						} else {
							alertify.error("{{ print_js_string( Lang::get('general.messages.error') ) }}");
						}
					},
					error: function() {
						LOADING.hide();
						alertify.error("{{ print_js_string( Lang::get('general.messages.error') ) }}");
					}
				});

			});

			TICKETS.init('#tab-tickets');

			TICKETS.cont.on('click', '.pagination a, thead a', function(e){
				e.preventDefault();
				if ( url = $(this).attr('href') ) {
					LOADING.show();
					TICKETS.cont.load(url, function(){
						LOADING.hide();
					});
				}
			});

			TICKETS.reload();

			$.ajax({
				type: 'GET',
				dataType: 'json',
				url: '{{ action('Account\Visits\AjaxController@getTab') }}',
				data: {
					user_id: {{ $employee->id }}
				},
				success: function(data) {
					if ( data.success ) {
						$('#account-visits-ajax-tab').html( data.html );
					} else {
						$('#account-visits-ajax-tab').html('<div class="alert alert-danger">{{ print_js_string( Lang::get('general.messages.error') ) }}</div>')
					}
				},
				error: function() {
						$('#account-visits-ajax-tab').html('<div class="alert alert-danger">{{ print_js_string( Lang::get('general.messages.error') ) }}</div>')
				}
			});

		});
	</script>

@endsection
