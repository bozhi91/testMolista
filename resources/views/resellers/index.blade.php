@extends('layouts.resellers')

@section('content')

	<style media="screen">
		.table-total { font-size: 18px; font-weight: bold; }
	</style>

	<div class="container">

		<h1 class="list-title">{{ Lang::get('resellers.commissions') }}</h1>

		@if ( $commissions->count() < 1)
			<div class="alert alert-info" role="alert">{{ Lang::get('resellers.commissions.empty') }}</div>
		@else
			<table class="table table-striped">
				<thead>
					<tr>
						{!! drawSortableHeaders(url()->full(), [
							'created' => [ 'title' => Lang::get('resellers.commissions.created'), 'class'=>'text-center', 'sortable'=>false, ],
							'site' => [ 'title' => Lang::get('resellers.commissions.site'), 'sortable'=>false, ],
							'amount_pending' => [ 'title' => Lang::get('resellers.commissions.amount_pending'), 'class'=>'text-right', 'sortable'=>false, ],
							'amount' => [ 'title' => Lang::get('resellers.commissions.amount_paid'), 'class'=>'text-right', 'sortable'=>false, ],
							'paid' => [ 'title' => Lang::get('resellers.commissions.paid'), 'class'=>'text-center', 'sortable'=>false, ],
							'paydate' => [ 'title' => Lang::get('resellers.commissions.paydate'), 'class'=>'text-center text-nowrap', 'sortable'=>false, ],
						]) !!}
					</tr>
				</thead>
				<tbody>
					@foreach ($commissions as $commission)
						<?php
							$commission->infocurrency->decimals = 2;
						?>
						<tr>
							<td class="text-center">{{ $commission->created_at->format('d/m/Y') }}</td>
							<td>
								@if ( $commission->site )
									{{ $commission->site->main_url }}
								@endif
							</td>
							<td class="text-right">{{ (!$commission->reseller_paid) ? price($commission->reseller_amount, $commission->infocurrency) : '' }}</td>
							<td class="text-right">{{ ($commission->reseller_paid) ? price($commission->reseller_amount, $commission->infocurrency) : '' }}</td>
							<td class="text-center"><span class="glyphicon glyphicon-{{ $commission->reseller_paid ? 'ok' : 'remove' }}" aria-hidden="true"></span></td>
							<td class="text-center">
								@if ( $commission->reseller_date )
									{{ $commission->reseller_date->format('d/m/Y') }}
								@endif
							</td>
						</tr>
					@endforeach
					<tr>
						<td></td>
						<td></td>
						<td class="text-right table-total">{{ price($commissions->where('reseller_paid', 0)->sum('reseller_amount'), $commission->infocurrency) }}</td>
						<td class="text-right table-total">{{ price($commissions->where('reseller_paid', 1)->sum('reseller_amount'), $commission->infocurrency) }}</td>
						<td></td>
						<td></td>
					</tr>
				</tbody>
			</table>
			{!! drawPagination($commissions, Input::except('page')) !!}
		@endif

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
		});
	</script>

@endsection
