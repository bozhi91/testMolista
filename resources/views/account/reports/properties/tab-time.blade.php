<div class="text-right">
	<div class="btn-group" role="group">
		<a href="{{ action('Account\Reports\PropertiesController@getPeriod', '7-days') }}" class="btn {{ ($period == '7-days') ? 'btn-primary' : 'btn-default' }}">{{ Lang::get('account/reports.time.days.7') }}</a>
		<a href="{{ action('Account\Reports\PropertiesController@getPeriod', '30-days') }}" class="btn {{ ($period == '30-days') ? 'btn-primary' : 'btn-default' }}">{{ Lang::get('account/reports.time.days.30') }}</a>
		<a href="{{ action('Account\Reports\PropertiesController@getPeriod', '60-days') }}" class="btn {{ ($period == '60-days') ? 'btn-primary' : 'btn-default' }}">{{ Lang::get('account/reports.time.days.60') }}</a>
		<a href="{{ action('Account\Reports\PropertiesController@getPeriod', '90-days') }}" class="btn {{ ($period == '90-days') ? 'btn-primary' : 'btn-default' }}">{{ Lang::get('account/reports.time.days.90') }}</a>
		<a href="{{ action('Account\Reports\PropertiesController@getPeriod', 'year-to-date') }}" class="btn {{ ($period == 'year-to-date') ? 'btn-primary' : 'btn-default' }}">{{ Lang::get('account/reports.time.year.to.date') }}</a>
	</div>
</div>

<div class="stats-area">

	<h3>{{ Lang::get('account/reports.published') }}</h3>
	<div class="row">
		<div class="col-xs-12 col-sm-3">
			<div class="panel panel-default panel-stats">
				<div class="panel-heading">{{ Lang::get('account/reports.sale') }}</div>
				<div class="panel-body">
					{{ number_format($stats->published_sale, 0, ',', '.') }}
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-3">
			<div class="panel panel-default panel-stats">
				<div class="panel-heading">{{ Lang::get('account/reports.rent') }}</div>
				<div class="panel-body">
					{{ number_format($stats->published_rent, 0, ',', '.') }}
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-3">
			<div class="panel panel-default panel-stats">
				<div class="panel-heading">{{ Lang::get('account/reports.transfer') }}</div>
				<div class="panel-body">
					{{ number_format($stats->published_transfer, 0, ',', '.') }}
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-3">
			<div class="panel panel-default panel-stats">
				<div class="panel-heading">{{ Lang::get('account/reports.total') }}</div>
				<div class="panel-body">
					{{ number_format($stats->published_sale+$stats->published_rent, 0, ',', '.') }}
				</div>
			</div>
		</div>
	</div>

	<h3>{{ Lang::get('account/reports.transactions') }}</h3>
	<div class="row">
		<div class="col-xs-12 col-sm-3">
			<div class="panel panel-default panel-stats">
				<div class="panel-heading">{{ Lang::get('account/reports.sale') }}</div>
				<div class="panel-body">
					{{ number_format($stats->total_sold, 0, ',', '.') }}
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-3">
			<div class="panel panel-default panel-stats">
				<div class="panel-heading">{{ Lang::get('account/reports.rent') }}</div>
				<div class="panel-body">
					{{ number_format($stats->total_rented, 0, ',', '.') }}
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-3">
			<div class="panel panel-default panel-stats">
				<div class="panel-heading">{{ Lang::get('account/reports.transfer') }}</div>
				<div class="panel-body">
					{{ number_format($stats->total_transfered, 0, ',', '.') }}
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-3">
			<div class="panel panel-default panel-stats">
				<div class="panel-heading">{{ Lang::get('account/reports.total') }}</div>
				<div class="panel-body">
					{{ number_format($stats->total_sold+$stats->total_rented, 0, ',', '.') }}
				</div>
			</div>
		</div>
	</div>

</div>
