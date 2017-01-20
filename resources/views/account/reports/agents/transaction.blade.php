@extends('layouts.popup')

@section('content')

<div id="account-container">

	<div id="popup-transactions" style="padding: 0 20px;">
		<h2 class="page-title">{{ Lang::get('account/reports.transactions') }}</h2>

		@if ( $transactions->count() < 1)
		<div class="alert alert-info">{{ Lang::get('account/reports.empty') }}</div>
		@else
		<table class="table table-striped">
			<thead>
			<th>{{ Lang::get('account/properties.show.transactions.date') }}</th>
			<th>{{ Lang::get('account/properties.ref') }}</th>
			<th>{{ Lang::get('account/properties.column.address') }}</th>
			<th>{{ Lang::get('account/properties.show.transactions.seller') }}</th>
			<th class="">{{ Lang::get('account/properties.show.transactions.buyer') }}</th>
			<th class="text-nowrap text-right">{{ Lang::get('account/properties.show.transactions.commission') }}</th>
			<th class="text-right">{{ Lang::get('account/properties.show.transactions.price') }}</th>
			</thead>
			<tbody>
				@foreach ($transactions as $catch)
				<tr>
					<td>{{ $catch->transaction_date ? $catch->transaction_date->format('d/m/Y') : $catch->catch_date->format('d/m/Y') }}</td>
					<td>{{ $catch->property->ref }}</td>
					<td>{{ $catch->property->address }}</td>
					<td>{{ $catch->seller_full_name }}</td>
					<td>
						@if ( $catch->buyer )
							{{ $catch->buyer->full_name }}
						@endif
					</td>
					<td class="text-right">
						@if ( $catch->status == 'sold' || $catch->status == 'rent' || $catch->status == 'transfer' )
							{{ price($catch->commission_earned, $catch->property->infocurrency) }}
						@endif
					</td>
					<td class="text-right">
						@if ( $catch->status == 'sold' || $catch->status == 'rent' || $catch->status == 'transfer' )
							{{ price($catch->price_sold, $catch->property->infocurrency) }}
						@endif
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
		{!! drawPagination($transactions, Input::except('page'), false) !!}
		@endif
	</div>

</div>

@endsection