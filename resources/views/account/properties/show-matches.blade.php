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
						{!! Form::open([ 'method'=>'POST', 'action'=>['Account\CustomersController@getAddPropertyCustomer', $property->slug] ]) !!}
							{!! Form::hidden('customer_id', $customer->id) !!}
							{!! Form::hidden('current_tab', 'tab-lead') !!}
							<button type="submit" class="btn btn-default btn-xs">
								{{ Lang::get('account/properties.show.leads.add') }}
							</button>
						{!! Form::close() !!}
					</td>
				</tr>
			@endforeach
		</tbody>
	</table>

@endif
