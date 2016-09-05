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
					{!! Form::hidden('limit', Input::get('limit')) !!}
					<div class="form-group">
						{!! Form::label('customer_id', Lang::get('account/customers.h1'), [ 'class'=>'sr-only' ]) !!}
						{!! Form::select('customer_id', [
							'' => '&nbsp;',
						]+$current_site->getCustomersOptions($current_site_user), Input::get('customer_id'), [ 'class'=>'has-select-2 form-control' ]) !!}
					</div>
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
								'name' => [ 'title' => Lang::get('account/customers.name'), 'sortable'=>false, ],
								'email' => [ 'title' => Lang::get('account/customers.email'), 'sortable'=>false, ],
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
								<th class="text-center">{{ number_format($customer->properties->count(), 0, ',', '.') }}</td>
								<th class="text-center">{{ number_format($customer->possible_matches->count(), 0, ',', '.') }}</td>
								<td class="text-right text-nowrap">
									<a href="{{ action('Account\CustomersController@show', urlencode($customer->email)) }}" class="btn btn-primary btn-xs">{{ Lang::get('general.view') }}</a>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
                {!! drawPagination($customers, Input::except('page')) !!}
			@endif

		</div>
	</div>

	<script type="text/javascript">
		ready_callbacks.push(function() {
			var cont = $('#admin-customers');
			var form = $('#filters-form');
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

			form.find('.has-select-2').select2();

		});
	</script>

@endsection