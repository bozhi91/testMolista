@if ( empty($customers) || $customers->count() < 1 )
	<div class="alert alert-info">
		{{ Lang::get('account/properties.show.leads.empty') }}
	</div>

@else
	<table class="table table-striped">
		<thead>
			<tr>
				{!! drawSortableHeaders(url()->full(), [
					'name' => [ 'title' => Lang::get('account/customers.name'), 'sortable'=>false, ],
					'email' => [ 'title' => Lang::get('account/customers.email'), 'sortable'=>false, ],
					'phone' => [ 'title' => Lang::get('account/customers.phone'), 'sortable'=>false, ],
					'origin' => [ 'title' => Lang::get('account/customers.origin'), 'sortable'=>false, ],
					'action' => [ 'title' => '', 'sortable'=>false, 'class'=>'text-right text-nowrap', ],
				]) !!}
			</tr>
		</thead>
		<tbody>
			@foreach ($customers as $customer)
				<tr>
					<td>{{ $customer->full_name }}</td>
					<td>{{ $customer->email }}</td>
					<td>{{ $customer->phone }}</td>
					<td style="text-transform: capitalize;">{{ $customer->origin }}</td>
					<td class="text-right text-nowrap">
						<a href="{{ action('Account\CustomersController@show', urlencode($customer->email)) }}" class="btn btn-primary btn-xs" target="_blank">{{ Lang::get('general.view') }}</a>
					</td>
				</tr>
			@endforeach
		</tbody>
	</table>

@endif
