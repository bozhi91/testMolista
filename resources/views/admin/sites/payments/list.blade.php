@if ( $payments->count() < 1 )
	<div class="alert alert-info">
		{{ Lang::get('admin/sites.payments.empty') }}
	</div>
@else
	<table class="table">
		<thead>
			<tr>
				<th>{{ Lang::get('admin/sites.payments.date') }}</th>
				<th class="text-right">{{ Lang::get('admin/sites.payments.amount') }}</th>
				<th>{{ Lang::get('admin/sites.payments.method') }}</th>
				<th>{{ Lang::get('admin/sites.payments.reseller') }}</th>
				<th class="text-right">{{ Lang::get('admin/sites.payments.commission') }}</th>
				<th class="text-center">{{ Lang::get('admin/sites.payments.paid') }}</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			@foreach ($payments as $payment)
				<tr>
					<td>{{ $payment->created_at->format('d/m/Y') }}</td>
					<td class="text-right">{{ price($payment->payment_amount, $payment->infocurrency) }}</td>
					<td>{{ Lang::get("account/payment.method.{$payment->payment_method}") }}</td>
					<td>
						@if ( @$payment->reseller )
							{{ $payment->reseller->name }}
						@endif
					</td>
					<td class="text-right">
						@if ( @$payment->reseller_amount > 0 )
							{{ price($payment->reseller_amount, $payment->infocurrency) }}
						@endif
					</td>
					<td class="text-center">
						@if ( @$payment->reseller )
							<span class="glyphicon glyphicon-{{ $payment->reseller_paid ? 'ok' : 'remove' }}" aria-hidden="true"></span>
						@endif
					</td>
					<td class="text-right text-nowrap">
						<a href="#" data-href="{{ action('Admin\Sites\PaymentsController@getEdit', $payment->id) }}" class="btn btn-xs btn-default edit-payment-trigger" target="_blank">{{ Lang::get('admin/sites.payments.button') }}</a>
					</td>
				</tr>
			@endforeach
		</tbody>
	</table>
	{!! drawPagination($payments, Input::only('limit')) !!}
@endif