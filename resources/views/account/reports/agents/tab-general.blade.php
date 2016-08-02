<?php
	$stats_blocks = [
		'captured' => true,
		'visited' => true,
		'closed' => true,
	];
?>

{!! Form::open([ 'action'=>'Account\Reports\AgentsController@getIndex', 'method'=>'get', 'id'=>'filters-form', 'class'=>'form-inline text-right' ]) !!}
	{!! Form::hidden('period', Input::get('period','7-days')) !!}
	{!! Form::select('agent', [ ''=>Lang::get('account/reports.agents.all') ]+$managers, Input::get('agent'), [ 'class'=>'form-control pull-left' ]) !!}
	<div class="btn-group" role="group">
		<button type="button" data-period="7-days" class="btn-period btn {{ (Input::get('period','7-days') == '7-days') ? 'btn-primary' : 'btn-default' }}">{{ Lang::get('account/reports.time.days.7') }}</button>
		<button type="button" data-period="30-days" class="btn-period btn {{ (Input::get('period') == '30-days') ? 'btn-primary' : 'btn-default' }}">{{ Lang::get('account/reports.time.days.30') }}</button>
		<button type="button" data-period="60-days" class="btn-period btn {{ (Input::get('period') == '60-days') ? 'btn-primary' : 'btn-default' }}">{{ Lang::get('account/reports.time.days.60') }}</button>
		<button type="button" data-period="90-days" class="btn-period btn {{ (Input::get('period') == '90-days') ? 'btn-primary' : 'btn-default' }}">{{ Lang::get('account/reports.time.days.90') }}</button>
		<button type="button" data-period="year-to-date" class="btn-period btn {{ (Input::get('period') == 'year-to-date') ? 'btn-primary' : 'btn-default' }}">{{ Lang::get('account/reports.time.year.to.date') }}</button>
	</div>
{!! Form::close() !!}

<div class="stats-area">

	@if ( !empty($stats_blocks['captured']) )
		<h3>{{ Lang::get('account/reports.catched') }}</h3>
		<div class="row">
			<div class="col-xs-12 col-sm-4">
				<div class="panel panel-default panel-stats">
					<div class="panel-heading">{{ Lang::get('account/reports.sale') }}</div>
					<div class="panel-body">
						{{ number_format($stats->published_sale, 0, ',', '.') }}
					</div>
				</div>
			</div>
			<div class="col-xs-12 col-sm-4">
				<div class="panel panel-default panel-stats">
					<div class="panel-heading">{{ Lang::get('account/reports.rent') }}</div>
					<div class="panel-body">
						{{ number_format($stats->published_rent, 0, ',', '.') }}
					</div>
				</div>
			</div>
			<div class="col-xs-12 col-sm-4">
				<div class="panel panel-default panel-stats">
					<div class="panel-heading">{{ Lang::get('account/reports.total') }}</div>
					<div class="panel-body">
						{{ number_format($stats->published_sale+$stats->published_rent, 0, ',', '.') }}
					</div>
				</div>
			</div>
		</div>
	@endif

	@if ( !empty($stats_blocks['visited']) )
		<h3>{{ Lang::get('account/reports.visits') }}</h3>
		<div class="row">
			<div class="col-xs-12 col-sm-4">
				<div class="panel panel-default panel-stats">
					<div class="panel-heading">{{ Lang::get('account/reports.sale') }}</div>
					<div class="panel-body">
						{{ number_format($stats->visits_sale, 0, ',', '.') }}
					</div>
				</div>
			</div>
			<div class="col-xs-12 col-sm-4">
				<div class="panel panel-default panel-stats">
					<div class="panel-heading">{{ Lang::get('account/reports.rent') }}</div>
					<div class="panel-body">
						{{ number_format($stats->visits_rent, 0, ',', '.') }}
					</div>
				</div>
			</div>
			<div class="col-xs-12 col-sm-4">
				<div class="panel panel-default panel-stats">
					<div class="panel-heading">{{ Lang::get('account/reports.total') }}</div>
					<div class="panel-body">
						{{ number_format($stats->visits_sale+$stats->visits_rent, 0, ',', '.') }}
					</div>
				</div>
			</div>
		</div>
	@endif

	@if ( !empty($stats_blocks['closed']) )
		<h3>{{ Lang::get('account/reports.transactions') }}</h3>
		<div class="row">
			<div class="col-xs-12 col-sm-4">
				<div class="panel panel-default panel-stats">
					<div class="panel-heading">{{ Lang::get('account/reports.sale') }}</div>
					<div class="panel-body">
						{{ number_format($stats->total_sold, 0, ',', '.') }}
					</div>
				</div>
			</div>
			<div class="col-xs-12 col-sm-4">
				<div class="panel panel-default panel-stats">
					<div class="panel-heading">{{ Lang::get('account/reports.rent') }}</div>
					<div class="panel-body">
						{{ number_format($stats->total_rented, 0, ',', '.') }}
					</div>
				</div>
			</div>
			<div class="col-xs-12 col-sm-4">
				<div class="panel panel-default panel-stats">
					<div class="panel-heading">{{ Lang::get('account/reports.total') }}</div>
					<div class="panel-body">
						{{ number_format($stats->total_sold+$stats->total_rented, 0, ',', '.') }}
					</div>
				</div>
			</div>
		</div>
	@endif

</div>

<script type="text/javascript">
	ready_callbacks.push(function(){
		var form = $('#filters-form');

		form.on('change','select[name="agent"]', function(){
			LOADING.show();
			form.submit();
		});

		form.on('click','.btn-period', function(){
			var el = $(this);

			if ( el.hasClass('btn-primary') ) {
				return false;
			}

			form.find('input[name="period"]').val( el.data().period );

			LOADING.show();
			form.submit();
		});
	});
</script>