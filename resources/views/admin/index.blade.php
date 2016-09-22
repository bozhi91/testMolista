@extends('layouts.admin')

@section('content')

	<div class="container">
		<div class="row">
			<div class="col-xs-12">

				@if (session('status'))
					<div class="alert alert-success alert-dismissible">
						<button type="button" class="close" data-dismiss="alert" aria-label="{{ Lang::get('general.messages.close') }}"><span aria-hidden="true">&times;</span></button>
						{{ session('status') }}
					</div>
				@endif

				<div class="row">
					<div class="col-xs-12 col-md-8">
						<h1>Admin home</h1>
					</div>
					<div class="col-xs-12 col-md-4">
						{!! Form::model(null, [ 'id'=>'filter-form', 'method'=>'get' ]) !!}
							{!! Form::text('daterange', Input::get('daterange'), [ 'class'=>'form-control pull-right daterange-input' ]) !!}
						{!! Form::close() !!}
					</div>
				</div>

				<div class="row">
					<div class="col-sm-6">
						<h3>New contracts ({{ Input::get('daterange') }})</h3>
						<div class="row">
							<div class="col-sm-4">
								<div class="panel panel-default text-center">
									<div class="panel-heading">Free</div>
									<div class="panel-body">
										<big>{{ @number_format($items->where('plan_level', 0)->count(), 0, ',', '.') }}</big>
									</div>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="panel panel-default text-center">
									<div class="panel-heading">Pro</div>
									<div class="panel-body">
										<big>{{ @number_format($items->where('plan_level', 1)->count(), 0, ',', '.') }}</big>
									</div>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="panel panel-default text-center">
									<div class="panel-heading">Plus</div>
									<div class="panel-body">
										<big>{{ @number_format($items->where('plan_level', 2)->count(), 0, ',', '.') }}</big>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-sm-6">
						<h3>Current totals</h3>
						<div class="row">
							<div class="col-sm-4">
								<div class="panel panel-default text-center">
									<div class="panel-heading">Total Free</div>
									<div class="panel-body">
										<big>{{ @number_format($stats[0]->total_sites, 0, ',', '.') }}</big>
									</div>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="panel panel-default text-center">
									<div class="panel-heading">Total Paying</div>
									<div class="panel-body">
										<big>{{ @number_format($stats[1]->total_sites+$stats[2]->total_sites, 0, ',', '.') }}</big>
									</div>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="panel panel-default text-center">
									<div class="panel-heading">Monthly Revenue</div>
									<div class="panel-body">
										<big>{{ @number_format($stats[1]->total_revenues+$stats[2]->total_revenues, 0, ',', '.') }}</big>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				@if ( $items->count() )
					<h3>New contracts by location</h3>
					<div id="sites-map" style="height: 400px; margin-bottom: 10px;"></div>
					<ul class="list-inline">
						<li><img src="{{ asset('images/admin/markers/paid.png') }}" alt="" /></li>
						<li>Paid plan</li>
						<li><img src="{{ asset('images/admin/markers/free.png') }}" alt="" /></li>
						<li>Free plan</li>
					</ul>
				@endif

			</div>
		</div>
	</div>

	<script type="text/javascript">
		var map_window,
			map_items = {!! json_encode($items) !!};

		if ( map_items ) {
			google.maps.event.addDomListener(window, 'load', function(){
				var bounds = new google.maps.LatLngBounds();

				var sites_map = new google.maps.Map(document.getElementById('sites-map'), {
					zoom: 6,
					center: { lat: {!! config('app.lat_default') !!}, lng: {{ config('app.lng_default') }} }
				});

				$.each(map_items, function(k,v){
					var position = { lat: parseFloat(v.lat), lng: parseFloat(v.lng) };

					if ( v.plan_level > 0 ) {
						var icon = '{{ asset('images/admin/markers/paid.png') }}';
					} else {
						var icon = '{{ asset('images/admin/markers/free.png') }}';
					}

					var marker = new google.maps.Marker({
						position: position,
						map: sites_map,
						icon: icon
					});

					var info = new google.maps.InfoWindow({
						content: v.infowindow
					});

					marker.addListener('click', function() {
						if (map_window) {
							map_window.close();
						}
						info.open(sites_map, marker);
						map_window = info;
					});

					bounds.extend(position);
				});

				sites_map.fitBounds(bounds);
			});
		}


		ready_callbacks.push(function(){
			var form = $('#filter-form');

			form.validate({
				submitHandler: function(f) {
					LOADING.show();
					f.submit();
				}
			});

			form.find('input[name="daterange"]').daterangepicker({
				buttonClasses: 'btn btn-sm',
				applyClass: 'btn-default',
				cancelClass: 'btn-default',
				ranges: {
					'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
					'Últimos 3 días': [moment().subtract(4, 'days'), moment()],
					'Última semana': [moment().subtract(7, 'days'), moment()],
					'Último mes': [moment().subtract(30, 'days'), moment()],
					'Últimos 3 meses': [moment().subtract(90, 'days'), moment()],
					'Último año': [moment().subtract(365, 'days'), moment()]
				},
				locale: {
					format: "DD/MM/YYYY",
					applyLabel: 'Aplicar',
					cancelLabel: 'Cancelar',
					fromLabel: 'Desde',
					toLabel: 'Hasta',
					customRangeLabel: 'Rango personalizado',
					daysOfWeek: ['Do','Lu','Ma','Mi','Ju','Vi','Sa'],
					monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
					firstDay: 1
				}
			}).on('apply.daterangepicker', function(ev, picker) {
				form.submit();
			});

		});
	</script>

@endsection
