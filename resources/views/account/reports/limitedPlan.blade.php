

	<div id="account-reports" class="account-reports">

		<h1 class="page-title">{{ Lang::get('account/menu.reports') }}</h1>
		<h3>{{ Lang::get('account/reports.abstract.properties.title') }}</h3>
		<div class="row">
			<div class="col-xs-12 col-sm-4">
				<div class="panel panel-default text-center">
					<div class="panel-heading">
						<h3 class="panel-title">{{ Lang::get('account/reports.abstract.properties.total.active') }}</h3>
					</div>
					<div class="panel-body">
						No disponible en tu plan. <a href="{{$url}}">Actualizar</a>
					</div>
				</div>
			</div>
			<div class="col-xs-12 col-sm-4">
				<div class="panel panel-default text-center">
					<div class="panel-heading">
						<h3 class="panel-title">{{ Lang::get('account/reports.abstract.properties.total.sale.price') }}</h3>
					</div>
					<div class="panel-body">
						No disponible en tu plan. <a href="{{$url}}">Actualizar</a>
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
						No disponible en tu plan. <a href="{{$url}}">Actualizar</a>
					</div>
				</div>
			</div>
			<div class="col-xs-12 col-sm-4">
				<div class="panel panel-default text-center">
					<div class="panel-heading">
						<h3 class="panel-title">{{ Lang::get('account/reports.abstract.leads.since') }}</h3>
					</div>
					<div class="panel-body">
						No disponible en tu plan. <a href="{{$url}}">Actualizar</a>
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
						No disponible en tu plan.  <a href="{{$url}}">Actualizar</a>
					</div>
				</div>
			</div>
		</div>
	</div>


