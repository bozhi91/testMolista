<div class="stats-area">

	<h3>{{ Lang::get('account/reports.leads.period') }}</h3>
	<div class="row">
		<div class="col-xs-12 col-sm-4">
			<div class="panel panel-default panel-stats">
				<div class="panel-heading">{{ Lang::get('account/reports.time.days.7') }}</div>
				<div class="panel-body">
					{{ number_format($stats['7-days'], 0, ',', '.') }}
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-4">
			<div class="panel panel-default panel-stats">
				<div class="panel-heading">{{ Lang::get('account/reports.time.days.30') }}</div>
				<div class="panel-body">
					{{ number_format($stats['30-days'], 0, ',', '.') }}
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-4">
			<div class="panel panel-default panel-stats">
				<div class="panel-heading">{{ Lang::get('account/reports.time.days.60') }}</div>
				<div class="panel-body">
					{{ number_format($stats['60-days'], 0, ',', '.') }}
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-4">
			<div class="panel panel-default panel-stats">
				<div class="panel-heading">{{ Lang::get('account/reports.time.days.90') }}</div>
				<div class="panel-body">
					{{ number_format($stats['90-days'], 0, ',', '.') }}
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-4">
			<div class="panel panel-default panel-stats">
				<div class="panel-heading">{{ Lang::get('account/reports.time.year.to.date') }}</div>
				<div class="panel-body">
					{{ number_format($stats['year-to-date'], 0, ',', '.') }}
				</div>
			</div>
		</div>
	</div>

	<h3>{{ Lang::get('account/reports.leads.current') }}</h3>
	<div class="row">
		<div class="col-xs-12 col-sm-4">
			<div class="panel panel-default panel-stats">
				<div class="panel-heading">{{ Lang::get('account/reports.total') }}</div>
				<div class="panel-body">
					{{ number_format($stats['current'], 0, ',', '.') }}
				</div>
			</div>
		</div>
	</div>

</div>
