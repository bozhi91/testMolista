@extends('layouts.account')

@section('account_content')

	<div id="admin-customers" class="row">
		<div class="col-xs-12">

	        @include('common.messages', [ 'dismissible'=>true ])

			<h1 class="page-title">{{ Lang::get('account/customers.h1') }}</h1>

			@if ( count($customers) < 1)
				<div class="alert alert-info">{{ Lang::get('account/customers.empty') }}</div>
			@else
				<table class="table table-striped">
					<thead>
						<tr>
							<th>{{ Lang::get('account/customers.name') }}</th>
							<th>{{ Lang::get('account/customers.email') }}</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						@foreach ($customers as $customer)
							<tr>
								<td>{{ $customer->full_name }}</td>
								<td>{{ $customer->email }}</td>
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
		});
	</script>

@endsection