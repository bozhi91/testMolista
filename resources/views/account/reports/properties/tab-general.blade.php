<div class="stats-area">

	<h3>{{ Lang::get('account/reports.published') }}</h3>
	<div class="row">
		<div class="col-xs-12 col-sm-4">
			<div class="panel panel-default panel-stats">
				<div class="panel-heading">{{ Lang::get('account/reports.sale') }}</div>
				<div class="panel-body">
					{{ number_format($stats->current_sale, 0, ',', '.') }}
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-4">
			<div class="panel panel-default panel-stats">
				<div class="panel-heading">{{ Lang::get('account/reports.rent') }}</div>
				<div class="panel-body">
					{{ number_format($stats->current_rent, 0, ',', '.') }}
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-4">
			<div class="panel panel-default panel-stats">
				<div class="panel-heading">{{ Lang::get('account/reports.total') }}</div>
				<div class="panel-body">
					{{ number_format($stats->current_sale+$stats->current_rent, 0, ',', '.') }}
				</div>
			</div>
		</div>
	</div>

	<h3>{{ Lang::get('account/reports.price') }}</h3>
	<div class="row">
		<div class="col-xs-12 col-sm-4">
			<div class="panel panel-default panel-stats">
				<div class="panel-heading">{{ Lang::get('account/reports.sale') }}</div>
				<div class="panel-body">
					{{ price($stats->current_sale_price) }}
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-4">
			<div class="panel panel-default panel-stats">
				<div class="panel-heading">{{ Lang::get('account/reports.rent') }}</div>
				<div class="panel-body">
					{{ price($stats->current_rent_price) }}
				</div>
			</div>
		</div>
	</div>

	<h3>{{ Lang::get('account/reports.price.sqm') }}</h3>
	<div class="row">
		<div class="col-xs-12 col-sm-4">
			<div class="panel panel-default panel-stats">
				<div class="panel-heading">{{ Lang::get('account/reports.sale') }}</div>
				<div class="panel-body">
					{{ price($stats->current_sale_sqm) }}/m²
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-4">
			<div class="panel panel-default panel-stats">
				<div class="panel-heading">{{ Lang::get('account/reports.rent') }}</div>
				<div class="panel-body">
					{{ price($stats->current_rent_sqm) }}/m²
				</div>
			</div>
		</div>
	</div>

</div>