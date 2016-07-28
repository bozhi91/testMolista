@if ( $invoices->count() < 1 )
	<div class="alert alert-info">{{ Lang::get('account/payment.invoices.empty') }}</div>
@else
	<table class="table">
		<thead>
			<tr>
				<th>{{ Lang::get('account/payment.invoices.uploaded_at') }}</th>
				<th>{{ Lang::get('account/payment.invoices.reference') }}</th>
				<th class="text-right">{{ Lang::get('account/payment.invoices.amount') }}</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			@foreach ($invoices as $invoice)
				<tr>
					<td>{{ $invoice->uploaded_at->format('d/m/Y') }}</td>
					<td>{{ $invoice->title }}</td>
					<td class="text-right">{{ price($invoice->amount, $current_site->infopaymentcurrency->toArray()) }}</td>
					<td class="text-right">
						<a href="{{ action('Account\InvoicesController@getInvoice', [ $invoice->id, $invoice->invoice_filename ]) }}" class="btn btn-xs btn-primary" target="_blank">{{ Lang::get('general.view') }}</a>
					</td>
				</tr>
			@endforeach
		</tbody>
	</table>
	{!! drawPagination($invoices, Input::only('limit')) !!}
@endif