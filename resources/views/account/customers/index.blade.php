@extends('layouts.account')

@section('account_content')

	<div id="admin-customers" class="row">
		<div class="col-xs-12">

	        @include('common.messages', [ 'dismissible'=>true ])

			<div class="pull-right">
				<a href="{{ action('Account\CustomersController@create') }}" class="btn btn-primary">{{ Lang::get('account/customers.button.new') }}</a>
			</div>

			<h1 class="page-title">{{ Lang::get('account/customers.h1') }}</h1>

			<div class="search-filters">
				@if ( !empty($clean_filters) )
					<a href="?limit={{ Input::get('limit') }}" class="text-bold pull-right">{{ Lang::get('general.filters.clean') }}</a>
				@endif
				<h2>{{ Lang::get('general.filters') }}</h2>
				{!! Form::open([ 'method'=>'GET', 'class'=>'form-inline', 'id'=>'filters-form' ]) !!}
					<div class="form-group">
						{!! Form::label('active', Lang::get('account/customers.active'), [ 'class'=>'sr-only' ]) !!}
						{!! Form::select('active', [
							'' => '',
							'1' => Lang::get('account/customers.active'),
							'0' => Lang::get('account/customers.active.not'),
						], Input::get('active'), [ 'class'=>'form-control' ]) !!}
					</div>
					<div class="form-group">
						{!! Form::label('name', Lang::get('account/customers.name'), [ 'class'=>'sr-only' ]) !!}
						{!! Form::text('name', Input::get('name'), [ 'class'=>'form-control', 'placeholder'=>Lang::get('account/customers.name') ]) !!}
					</div>
					<div class="form-group">
						{!! Form::label('email', Lang::get('account/customers.email'), [ 'class'=>'sr-only' ]) !!}
						{!! Form::text('email', Input::get('email'), [ 'class'=>'form-control', 'placeholder'=>Lang::get('account/customers.email') ]) !!}
					</div>
<!--					<div class="form-group">
						{!! Form::label('created_at', Lang::get('account/customers.created'), [ 'class'=>'sr-only' ]) !!}
						{!! Form::text('created_at', Input::get('created_at'), [ 'class'=>'form-control', 'placeholder'=>Lang::get('account/customers.created') ]) !!}
					</div>-->
					<div class="form-group">
						{!! Form::label('origin', Lang::get('account/customers.origin'), [ 'class'=>'sr-only' ]) !!}
						{!! Form::select('origin', [
							'' => '',
							'web' => 'Web',
						], Input::get('origin'), [ 'class'=>'form-control' ]) !!}
					</div>
<!--					<div class="form-group">
						{!! Form::label('properties', Lang::get('account/customers.properties'), [ 'class'=>'sr-only' ]) !!}
						{!! Form::text('properties', Input::get('properties'), [ 'class'=>'form-control', 'id' => 'not-properties', 'placeholder'=>Lang::get('account/customers.properties') ]) !!}
					</div>-->
					
					{!! Form::submit(Lang::get('general.view'), [ 'class'=>'btn btn-default' ]) !!}
				{!! Form::close() !!}
			</div>

			@if ( count($customers) < 1)
				<div class="alert alert-info">{{ Lang::get('account/customers.empty') }}</div>
			@else
				<table class="table table-striped">
					<thead>
						<tr>
							{!! drawSortableHeaders(url()->full(), [
								'status' => [ 'title' => Lang::get('account/customers.active'), ],
								'name' => [ 'title' => Lang::get('account/customers.name'), ],
								'email' => [ 'title' => Lang::get('account/customers.email'), ],
								'creation' => [ 'title' => Lang::get('account/customers.created') ],
								'origin' => [ 'title' => Lang::get('account/customers.origin'), ],
								'properties' => [ 'title' => Lang::get('account/customers.properties'), 'class'=>'text-center', ],
								'matches' => [ 'title' => Lang::get('account/customers.matches'), 'sortable'=>false, 'class'=>'text-center', ],
								'tickets' => [ 'title' => Lang::get('account/employees.tickets'), 'sortable'=>false, 'class'=>'text-center', ],
								'action' => [ 'title' => '', 'class'=>'text-right text-nowrap', ],
							]) !!}
						</tr>
					</thead>
					<tbody>
						@foreach ($customers as $customer)
							<tr>
								<td class="text-center">
									<a href="#" data-url="{{ action('Account\CustomersController@getChangeStatus', $customer->email) }}" class="change-status-trigger">
										<span class="glyphicon glyphicon-{{ $customer->active ? 'ok' : 'remove' }}" aria-hidden="true"></span>
									</a>
								</td>
								<td>{{ $customer->full_name }}</td>
								<td>{{ $customer->email }}</td>
								<td>{{  $customer->created_at->format('d/m/Y') }}</td>
								<td style="text-transform: capitalize;">{{ $customer->origin }}</td>
								<td class="text-center">{{ number_format($customer->properties->count(), 0, ',', '.') }}</td>
								<td class="text-center">{{ number_format($customer->possible_matches->count(), 0, ',', '.') }}</td>
								<td class="text-center">{{ @number_format(intval( $stats[$customer->id]->tickets->open ), 0, ',', '.') }}</td>
								<td class="text-right text-nowrap">
									<a href="{{ action('Account\CustomersController@show', urlencode($customer->email)) }}" class="btn btn-primary btn-xs">{{ Lang::get('general.view') }}</a>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
                {!! drawPagination($customers, Input::except('page'), action('Account\CustomersController@index', [ 'csv'=>1 ])) !!}
			@endif

		</div>
	</div>

	<script type="text/javascript">
		ready_callbacks.push(function() {
			var cont = $('#admin-customers');
			/*var form = $('#filters-form');
			var viewurl = "{{ action('Account\CustomersController@show', 'USEREMAIL') }}";

			form.validate({
				submitHandler: function(f) {
					var id = form.find('select[name="customer_id"]').val();

					if ( !id ) {
						return false;
					}

					document.location.href = viewurl.replace("USEREMAIL", id);
				}
			});

			form.find('.has-select-2').select2();*/

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


		});
	</script>

@endsection