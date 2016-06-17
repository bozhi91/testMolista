@extends('layouts.corporate')

@section('content')

	<div id="home" class="home">

			<!-- BANNER -->
			<div class="jumbotron">
			  <div class="container">
			  	<div class="col-md-6">
				    <h1>Tu Web Inmobiliaria</h1>
				    <p>Descubre <strong>molista</strong>, una <strong>herramienta web</strong> pensada específicamente <strong>para negocios inmobiliarios. Potente, atractiva, fácil de usar y totalmente adaptada a móviles y tabletas.</strong></p>
			    </div>
			  </div>
			</div>
			<!-- / BANNER -->

			<!-- FIRST BLOCK -->
			<section class="first-block">
				<div class="container">
					<div class="row">
						<div class="col-md-6">
							<h2>Multiplica las oportunidades de tu inmobiliaria</h2>
							<p><strong>molista</strong> te ofrece todo lo que necesitas para que <strong>tu inmobiliaria esté presente en internet:</strong></p>
							<ul>
								<li data-type="fixa">Fichas de inmuebles ilimitadas</li>
								<li data-type="searcher">Buscador de viviendas con filtros avanzados</li>
								<li data-type="relation">Posibilidad de relacionar inmuebles</li>
								<li data-type="easy">Fácil de usar y gestionar</li>
								<li data-type="settings">Personalizable</li>
								<li data-type="responsive">Compatible con móviles y tabletas</li>
							</ul>
						</div>
						<div class="col-md-6">
							<img class="img-responsive" src="{{ Theme::url('/images/corporate/responsive.png') }}">
						</div>

					</div>
				</div>
			</section>
			<!-- / FIRST BLOCK -->

			<!-- SECOND BLOCK -->
			<!-- BLOCK LINKS -->
			<section class="block-links">
				<div class="container">
					<div class="row">
						<div class="col-lg-6 col-lg-offset-3 clearfix">
						  <ul>
					        <li><button class="btn btnBdrYlw text-uppercase">VER DEMO</button></li>
					        <li><a href="{{ action('Corporate\FeaturesController@getIndex') }}" class="btn btnBdrYlw text-uppercase" >más información</a></li>
					      </ul>
						</div>
					</div>
				</div>
			</section>
			<!--/ BLOCK LINKS -->
			<!-- SECOND BLOCK -->

			<!-- THIRD BLOCK -->
			<section class="third-block">
				<div class="container">
					<div class="row">
						<div class="title-block col-md-6 col-md-offset-3 text-center">
						  <h3>Las prestaciones que necesitas</h3>
						</div>
					</div>
					<div class="row">
						<div class="col-md-10 col-md-offset-1">

						  	<div class="panel first-panel">
						  		<div class="row">
						  			<div class="col-md-4">
						  				<h4>Sencilla<h4>
						  			</div>
						  			<div class="col-md-8">
						  				<p>Su intuitivo panel de control te permite <strong>subir todo tu catálogo de propiedades fácilmente y en pocos clics.</strong></p>
						  			</div>

						  		</div>
						  	</div>

						  	<div class="panel second-panel">
						  		<div class="row">
						  			<div class="col-md-4">
						  				<h4>Potente<h4>
						  			</div>
						  			<div class="col-md-8">
						  				<p>Ofrece a tus clientes una <strong>potente herramienta de búsqueda</strong>, con campos avanzados, por características del inmueble, por localización... Con herramientas <strong>SEO</strong> y totalmente <strong>adaptada a tabletas y móviles</strong>.</p>
						  			</div>

						  		</div>
						  	</div>

						  	<div class="panel third-panel">
						  		<div class="row">
						  			<div class="col-md-4">
						  				<h4>Personalizable<h4>
						  			</div>
						  			<div class="col-md-8">
						  				<p>Adáptala a <strong>la imagen de tu empresa</strong> y <strong>personaliza los campos</strong> y las características de los inmuebles que quieras vender o alquilar mediante plantillas personalizables fáciles de usar.</p>
						  			</div>

						  		</div>
						  	</div>

						</div>
					</div>
				</div>
			</section>
			<!-- THIRD BLOCK -->

			<!-- FOURTH BLOCK -->
			<section class="fourth-block">
				<div class="container">
					<div class="row">
						<div class="title-block col-md-6 col-md-offset-3 text-center">
						  <h3>¿Todavía no estás convencido?</h3>
						</div>
					</div>
				</div>
				<div class="jumbotron-bottom">
					<div class="container">
						<div class="row">
							<div class="col-sm-4 col-sm-offset-2 col-md-3 col-md-offset-3 text-right">
							  <p>Comprueba en nuestra demo online todas las funcionalidades y ventajas que te ofrece <strong>molista.</strong></p>
							</div>
							<div class="col-sm-4 col-md-3 text-left">
							  <p>También puedes  probar <strong>molista</strong> durante 30 días gratis. Disfruta de todas sus ventajas sin compromiso.</p>
							</div>
						</div>
					</div>
				</div>
			</section>
			<!-- / FOURTH BLOCK -->
			<!-- BLOCK LINKS -->
			<section class="block-links">
				<div class="container">
					<div class="row">
						<div class="col-lg-6 col-lg-offset-3 clearfix">
						  <ul>
					        <li><button class="btn btnBdrYlw text-uppercase">VER DEMO</button></li>
					        <li><a href="{{ action('Corporate\FeaturesController@getIndex') }}" class="btn btnBdrYlw text-uppercase" >más información</a></li>
					      </ul>
						</div>
					</div>
				</div>
			</section>
			<!--/ BLOCK LINKS -->

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
			var cont = $('#home');

		});
	</script>

@endsection
