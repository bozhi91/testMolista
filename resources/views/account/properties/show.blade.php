@extends('layouts.account')

@section('account_content')

	<div id="admin-properties">

		@include('common.messages', [ 'dismissible'=>true ])

		<h1 class="page-title">{{ Lang::get('account/properties.edit.view') }}</h1>

		<ul class="nav nav-tabs main-tabs" role="tablist">
			<li role="presentation" class="active"><a href="#tab-general" aria-controls="tab-general" role="tab" data-toggle="tab">{{ Lang::get('account/properties.tab.general') }}</a></li>
			<li role="presentation"><a href="#tab-lead" aria-controls="tab-lead" role="tab" data-toggle="tab">{{ Lang::get('account/properties.tab.lead') }}</a></li>
			<li role="presentation"><a href="#tab-transaction" aria-controls="tab-transaction" role="tab" data-toggle="tab">{{ Lang::get('account/properties.tab.transaction') }}</a></li>
			<li role="presentation"><a href="#tab-reports" aria-controls="tab-reports" role="tab" data-toggle="tab">{{ Lang::get('account/properties.tab.reports') }}</a></li>
		</ul>

		<div class="tab-content">

			<div role="tabpanel" class="tab-pane tab-main active" id="tab-general">
				Info de la propiedad
			</div>

			<div role="tabpanel" class="tab-pane tab-main" id="tab-lead">
				<p>Lead es una persona interesada en el inmueble.</p>
				<p>Deberia ser el email que viene vinculado de Zendesk.</p>
				<p>Tambien podríamos crear Leads manualmente (Nombre, apellida, email, telefono de contacto, fecha de creacion de lead)</p>
			</div>

			<div role="tabpanel" class="tab-pane tab-main" id="tab-transaction">
				<p>Si se vende un inmueble se pone aqui:</p>
				<ul>
					<li>Qué lead lo ha comprado</li>
					<li>A qué precio</li>
					<li>Cuándo</li>
				</ul>
			</div>

			<div role="tabpanel" class="tab-pane tab-main" id="tab-reports">
				<p>Aquí mostramos KPIS comparativos:</p>
				<ul>
					<li>Cuántos contactos para vender vs. contactos promedio.</li>
					<li>Cuánto descuento aplicado sobre PVP inicial vs descuento promedio</li>
					<li>Días en venta antes de vender vs promedio de días para vender</li>
					<li>Vendido por: agente</li>
				</ul>
			</div>

		</div>

	</div>

@endsection