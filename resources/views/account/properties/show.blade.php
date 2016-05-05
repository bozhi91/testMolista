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
			<li role="presentation"><a href="#tab-logs" aria-controls="tab-logs" role="tab" data-toggle="tab">{{ Lang::get('account/properties.tab.logs') }}</a></li>
		</ul>

		<div class="tab-content">

			<div role="tabpanel" class="tab-pane tab-main active" id="tab-general">
				<p>Datos de la propiedad</p>
				<p>Fecha de captación y agente que lo captó</p>
				<p>
					Datos de contacto del vendedor
					<ul>
						<li>Nombre</li>
						<li>Apellido</li>
						<li>DNI</li>
						<li>Email</li>
						<li>Móvil</li>
						<li>Fijo</li>
					</ul>
				</p>
				<p>Precio más bajo por el que el propietario estaría dispuesto a vender</p>
				<p>Comisión acordada con el propietario (%)</p>
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

			<div role="tabpanel" class="tab-pane tab-main" id="tab-logs">
				@if ( count($property->logs) < 1 )
					<div class="alert">
						{{ Lang::get('account/properties.logs.empty') }}
					</div>
				@else
					<table class="table logs-table" data-toggle="table" data-sort-name="date" data-sort-order="desc">
						<thead>
							<th data-field="date" data-sortable="true" data-sort-name="_date_data" data-sorter="logDateSorter">{{ Lang::get('account/properties.logs.date') }}</th>
							<th data-field="responsible" data-sortable="false">{{ Lang::get('account/properties.logs.responsible') }}</th>
							<th data-field="action" data-sortable="false">{{ Lang::get('account/properties.logs.action') }}</th>
							<th data-field="options" data-sortable="false"></th>
						</thead>
						<tbody>
							@include('account.properties.logs', [ 'logs'=>$property->logs ])
						</tbody>
					</table>
				@endif
			</div>

		</div>

	</div>
<?php
echo "<pre>";
print_r($property->logs);
echo "</pre>";
?>
	<script type="text/javascript">
		function logDateSorter(a, b) {
			if (a.date < b.date) return -1;
			if (a.date > b.date) return 1;
			return 0;
		}

		ready_callbacks.push(function() {
			var cont = $('#admin-properties');

			cont.find('.popup-log-trigger').magnificPopup({
				type:'inline',
			});

		});
	</script>

@endsection