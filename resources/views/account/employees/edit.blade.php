@extends('layouts.account')

@section('account_content')

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
				</ul>

				<div class="tab-content">

					<div role="tabpanel" class="tab-pane tab-main active" id="tab-properties">
						<div class="alert alert-info properties-empty {{ ( count($properties) > 0 ) ? 'hide' : '' }}">{{ Lang::get('account/employees.show.tab.properties.empty') }}</div>
						@if ( count($properties) > 0 )
							<div class="properties-list {{ ( count($properties) < 1 ) ? 'hide' : '' }}">
								<table class="table table-hover">
									<thead>
										<tr>
											<th>{{ Lang::get('account/employees.show.tab.properties.title') }}</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										@foreach ($properties as $property)
											<tr class="property-line">
												<td>{{ $property->title }}</td>
												<td class="text-right text-nowrap">
													@if ( Auth::user()->can('property-edit') && Auth::user()->can('employee-edit'))
														{!! Form::button( Lang::get('account/employees.button.dissociate'), [ 'class'=>'btn btn-default btn-xs dissociate-trigger', 'data-url'=>action('Account\EmployeesController@getDissociate', [ $employee->id, $property->id ]) ]) !!}
													@endif
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						@endif
					</div>

					<div role="tabpanel" class="tab-pane tab-main" id="tab-permissions">
						<ul class="list-unstyled">
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

			cont.on('click','.dissociate-trigger',function(e){
				var el = $(this);
				e.preventDefault();
				SITECOMMON.confirm("{{ print_js_string( Lang::get('account/employees.show.tab.properties.dissociate') ) }}", function (e) {
					if (e) {
						LOADING.show();

						$.ajax({
							dataType: 'json',
							url: el.data().url,
							success: function(data) {
								LOADING.hide();
								if ( data.success ) {
									el.closest('.property-line').remove();
									if ( cont.find('.properties-list .property-line').length < 1 ) {
										cont.find('.properties-list').addClass('hide');
										cont.find('.properties-empty').removeClass('hide');
									}
									alertify.success("{{ print_js_string( Lang::get('account/employees.message.dissociated') ) }}");
								} else {
									alertify.error("{{ print_js_string( Lang::get('general.messages.error') ) }}");
								}
							},
							error: function() {
								LOADING.hide();
								alertify.error("{{ print_js_string( Lang::get('general.messages.error') ) }}");
							}
						});

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
		});
	</script>

@endsection