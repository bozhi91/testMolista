@extends('layouts.corporate')

@section('content')

	<div id="demo-page" >

		<div class="demo-intro">
			<div class="container">
				<div class="row">
					<div class="col-xs-12 col-sm-5">
						<h2>Una completa y atractiva web para tu inmobiliaria</h2>
						<ul class="list-unstyled">
							<li>Buscador de viviendas con filtros avanzados</li>
							<li>Posibilidad de relacionar inmuebles</li>
							<li>Fácil de usar y gestionar</li>
							<li>Personalizable</li>
							<li>Compatible con móviles y tabletas</li>
						</ul>
						<div class="xs-spacer"></div>
					</div>
					<div class="col-xs-12 col-sm-6 col-sm-offset-1">
						<img src="{{ asset('images/corporate/responsive.png') }}" class="img-responsive" alt="" />
					</div>
				</div>
			</div>
		</div>

		<div class="steps-intro">
			<div class="container">
				<div class="row">
					<div class="col-xs-12 col-sm-6 col-sm-offset-3">
						<h2>Una potente herramienta de gestión de propiedades, agentes y mucho más</h2>
					</div>
				</div>
			</div>
		</div>

		<div class="container">
			<div class="steps-block">
				<div class="row">
					<div class="col-xs-12 col-sm-4 col-sm-offset-4">
						<h2 style="background-image: url('{{ asset('images/corporate/demo/properties-h2.png') }}')">Propiedades</h2>
						<div class="intro-text">Crea y expón todo tu catálogo de inmuebles y gestiónalos desde un solo sitio.</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-6">
						<div class="image-block">
							<img src="{{ asset('images/corporate/demo/properties-01.jpg') }}" alt="" />
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="image-block">
							<img src="{{ asset('images/corporate/demo/properties-02.jpg') }}" alt="" />
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="image-block">
							<img src="{{ asset('images/corporate/demo/properties-03.jpg') }}" alt="" />
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="image-block">
							<img src="{{ asset('images/corporate/demo/properties-04.jpg') }}" alt="" />
						</div>
					</div>
				</div>
			</div>


			<div class="steps-block">
				<div class="row">
					<div class="col-xs-12 col-sm-6">
						<h2 style="background-image: url('{{ asset('images/corporate/demo/agents-h2.png') }}')">Agentes</h2>
						<div class="intro-text">Maneja y controla todos los inmuebles que gestionan y operan tus agentes sin que se pierda el historial agente-inmueble.</div>
						<div class="visible-xs image-block">
							<img src="{{ asset('images/corporate/demo/agents-01.jpg') }}" alt="" />
						</div>
						<div class="xs-spacer"></div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<h2 style="background-image: url('{{ asset('images/corporate/demo/leads-h2.png') }}')">Leads</h2>
						<div class="intro-text">Cada inmueble y los leads que ha generado en una misma pantalla, cómodo y accesible.</div>
						<div class="visible-xs image-block">
							<img src="{{ asset('images/corporate/demo/leads-01.jpg') }}" alt="" />
						</div>
					</div>
				</div>
				<div class="row hidden-xs">
					<div class="col-xs-12 col-sm-6">
						<div class="image-block">
							<img src="{{ asset('images/corporate/demo/agents-01.jpg') }}" alt="" />
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="image-block">
							<img src="{{ asset('images/corporate/demo/leads-01.jpg') }}" alt="" />
						</div>
					</div>
				</div>
			</div>

			<div class="steps-block">
				<div class="row">
					<div class="col-xs-12 col-sm-6 col-sm-offset-3">
						<h2 style="background-image: url('{{ asset('images/corporate/demo/tickets-h2.png') }}')">Tickets</h2>
						<div class="intro-text">Todos los correos que recibas de tus potenciales clientes se gestionan desde tu trastienda, para que no pierdas ni una demanda.</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-6">
						<div class="image-block">
							<img src="{{ asset('images/corporate/demo/tickets-01.jpg') }}" alt="" />
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="image-block">
							<img src="{{ asset('images/corporate/demo/tickets-02.jpg') }}" alt="" />
						</div>
					</div>
				</div>
			</div>

			<div class="steps-block">
				<div class="row">
					<div class="col-xs-12 col-sm-6">
						<h2 style="background-image: url('{{ asset('images/corporate/demo/reports-h2.png') }}')">Informes</h2>
						<div class="intro-text"> Informes de actividad de agentes, de inmuebles en tu cartera y de leads generados.</div>
						<div class="visible-xs image-block">
							<img src="{{ asset('images/corporate/demo/reports-01.jpg') }}" alt="" />
						</div>
						<div class="xs-spacer"></div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<h2 style="background-image: url('{{ asset('images/corporate/demo/exports-h2.png') }}')">Exportaciones</h2>
						<div class="intro-text">Exporta tu catálogo a las principales plataformas del sector inmobiliario.</div>
						<div class="visible-xs image-block">
							<img src="{{ asset('images/corporate/demo/exports-01.jpg') }}" alt="" />
						</div>
					</div>
				</div>
				<div class="row hidden-xs">
					<div class="col-xs-12 col-sm-6">
						<div class="image-block">
							<img src="{{ asset('images/corporate/demo/reports-01.jpg') }}" alt="" />
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="image-block">
							<img src="{{ asset('images/corporate/demo/exports-01.jpg') }}" alt="" />
						</div>
					</div>
				</div>
			</div>

		</div>

		<div id="home">
			@include('corporate.common.home-fourth-block')
		</div>

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
			var cont = $('#demo-page');
		});
	</script>

@endsection
