@extends('layouts.email')

@section('content')

	<h1>El <a href="{{ action('Admin\SitesController@edit', $site_id) }}" target="_blank">site {{ @$site_name ? $site_name : "ID {$site_id}" }}</a> ha modificado su plan.</h1>

	@if ( @$custom_message )
		<p>{{ $custom_message }}</p>
	@endif

	<table>
		<thead>
			<tr>
				<th></th>
				<th>Antes</th>
				<th>Ahora</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th>Plan</th>
				<td>{{ @$old_info['plan_name'] }}</td>
				<td>{{ @$new_info['plan_name'] }}</td>
			</tr>
			<tr>
				<th>Precio</th>
				<td>{{ @$old_info['plan_price'] }}</td>
				<td>{{ @$new_info['plan_price'] }}</td>
			</tr>
			<tr>
				<th>Pago</th>
				<td>
					@if ( @$old_info['payment_method'] == 'stripe' )
						{{ Lang::get('account/payment.method.stripe') }}
					@elseif ( @$old_info['payment_method'] == 'transfer' )
						{{ Lang::get('account/payment.method.transfer') }}<br />
						{{ @$old_info['iban_account'] }}
					@else
						{{ @$old_info['payment_method'] }}
					@endif
				</td>
				<td>
					@if ( @$new_info['payment_method'] == 'stripe' )
						{{ Lang::get('account/payment.method.stripe') }}
					@elseif ( @$new_info['payment_method'] == 'transfer' )
						{{ Lang::get('account/payment.method.transfer') }}<br />
						{{ @$new_info['iban_account'] }}
					@else
						{{ @$new_info['payment_method'] }}
					@endif
				</td>
			</tr>
		</tbody>
	</table>

@endsection
