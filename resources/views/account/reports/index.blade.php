@extends('layouts.account')

@section('account_content')

	<div id="account-reports" class="account-reports">

	 	@include('common.messages', [ 'dismissible'=>true ])

		<h1 class="page-title">{{ Lang::get('account/menu.reports') }}</h1>

		<h3>{{ Lang::get('account/reports.abstract.properties.title') }}</h3>
		<div class="row">
			<div class="col-xs-12 col-sm-4">
				<div class="panel panel-default text-center">
					<div class="panel-heading">
						<h3 class="panel-title">{{ Lang::get('account/reports.abstract.properties.total.active') }}</h3>
					</div>
					<div class="panel-body">
						{{ number_format($stats['properties_active'],0,',','.') }}
					</div>
				</div>
			</div>
			<div class="col-xs-12 col-sm-4">
				<div class="panel panel-default text-center">
					<div class="panel-heading">
						<h3 class="panel-title">{{ Lang::get('account/reports.abstract.properties.total.sale.price') }}</h3>
					</div>
					<div class="panel-body">
						{{ price($stats['properties_price'], $current_site->infocurrency) }}
					</div>
				</div>
			</div>
		</div>

		<h3>{{ Lang::get('account/reports.abstract.leads.title') }}</h3>
		<div class="row">
			<div class="col-xs-12 col-sm-4">
				<div class="panel panel-default text-center">
					<div class="panel-heading">
						<h3 class="panel-title">{{ Lang::get('account/reports.abstract.leads.total') }}</h3>
					</div>
					<div class="panel-body">
						{{ number_format($stats['leads_total'],0,',','.') }}
					</div>
				</div>
			</div>
			<div class="col-xs-12 col-sm-4">
				<div class="panel panel-default text-center">
					<div class="panel-heading">
						<h3 class="panel-title">{{ Lang::get('account/reports.abstract.leads.since') }}</h3>
					</div>
					<div class="panel-body">
						{{ number_format($stats['leads_since'],0,',','.') }}
					</div>
				</div>
			</div>
		</div>

		<h3>{{ Lang::get('account/reports.abstract.tickets.title') }}</h3>
		<div class="row">
			<div class="col-xs-12 col-sm-4">
				<div class="panel panel-default text-center">
					<div class="panel-heading">
						<h3 class="panel-title">{{ Lang::get('account/reports.abstract.tickets.open') }}</h3>
					</div>
					<div class="panel-body">
						{{ number_format($stats['tickets_open'],0,',','.') }}
					</div>
				</div>
			</div>
		</div>

		@if ( $stats['properties_top'] )
			<h3>{{ Lang::get('account/reports.abstract.properties.top') }}</h3>
			<table class="table table-striped">
				<thead>
					<tr>
						{!! drawSortableHeaders(url()->full(), [
							'reference' => [ 'title' => Lang::get('account/properties.ref'), 'sortable'=>false ],
							'title' => [ 'title' => Lang::get('account/properties.column.title'), 'sortable'=>false ],
							'location' => [ 'title' => Lang::get('account/properties.column.location'), 'sortable'=>false ],
							'lead' => [ 'title' => Lang::get('account/properties.tab.lead'), 'sortable'=>false, 'class'=>'text-center text-nowrap' ],
						]) !!}
					</tr>
				</thead>
				<tbody>
					@foreach ($stats['properties_top'] as $property)
						<tr>
							<td>{{ $property->ref }}</td>
							<td>{{ $property->title }}</td>
							<td>{{ @implode(' / ', array_filter([ $property->city->name, $property->state->name ])) }}</td>
							<td class="text-center">{{ number_format($property->customers->count(), 0, ',', '.')  }}</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		@endif

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
			var cont = $('#account-reports');

		});
	</script>

@endsection