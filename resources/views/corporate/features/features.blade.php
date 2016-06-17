@extends('layouts.corporate')

@section('content')

	<div id="features-banner">
		<div class="features-banner-padding">
			<h1>Características</h1>
		</div>
	</div>

	<div id="features">

		<div id="features-tab-selector">
			<div class="container">
				<div class="row">
					<div class="features-tab-selector-block">
						<ul class="nav nav-tabs nav-justified">
							<li class="active">
								<a data-toggle="tab" href="#feature-tab1">
									<div class="features-tab-selector-image">
										<img src="{{ Theme::url('/images/corporate/features/icon-tab-1.png') }}" class="img-responsive">
									</div>
									<div class="features-tab-selector-text hidden-xs">
										<p>Web</p>
									</div>
								</a>
							</li>
							<li>
								<a data-toggle="tab" href="#feature-tab2">
									<div class="features-tab-selector-image">
										<img src="{{ Theme::url('/images/corporate/features/icon-tab-2.png') }}" class="img-responsive">
									</div>
									<div class="features-tab-selector-text hidden-xs">
										<p>Inmueble</p>
									</div>
								</a>
							</li>
							<li>
								<a data-toggle="tab" href="#feature-tab3">
									<div class="features-tab-selector-image">
										<img src="{{ Theme::url('/images/corporate/features/icon-tab-3.png') }}" class="img-responsive">
									</div>
									<div class="features-tab-selector-text hidden-xs">
										<p>Agentes</p>
									</div>
								</a>
							</li>
							<li>
								<a data-toggle="tab" href="#feature-tab4">
									<div class="features-tab-selector-image">
										<img src="{{ Theme::url('/images/corporate/features/icon-tab-4.png') }}" class="img-responsive">
									</div>
									<div class="features-tab-selector-text hidden-xs">
										<p>Leads</p>
									</div>
								</a>
							</li>
							<li>
								<a data-toggle="tab" href="#feature-tab5">
									<div class="features-tab-selector-image">
										<img src="{{ Theme::url('/images/corporate/features/icon-tab-5.png') }}" class="img-responsive">
									</div>
									<div class="features-tab-selector-text hidden-xs">
										<p>Integraciones</p>
									</div>
								</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>

		<div id="features-content">
			<div class="container">
				<div class="tab-content">
					<div id="feature-tab1" class="tab-pane fade in active feature-tab-styles">
						<div class="row">
							<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
								<div class="features-content-text">
									<h1>Crea tu web <span class="hidden-xs"><br /></span> inmobiliaria</h1>
									<p>Dispones de <strong>todas la herramientas</strong> para crear tu web, publicar tus inmuebles y traducir los contenidos automáticamente a <strong>varios idiomas.</strong> Una web <strong>personalizable</strong> y 100% <strong>adaptada a tablets y dispositivos móviles.</strong></p>
									<p>Podrás elegir entre <strong>diferentes plantillas</strong> para darle el look que tú prefieras, incluir <strong>etiquetas SEO</strong> y añadir <strong>enlaces a tus redes sociales.</strong> Si ya dispones de web, puedes hacer <strong>el traspaso a nuestro sistema.<span class="hidden-xs"><br /></span> Pregúntanos cómo.</strong></p>
								</div>
							</div>
							<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
								<div class="feature-content-image">
									<img src="{{ Theme::url('/images/corporate/features/picture-tab-content-1.png') }}" class="img-responsive">
								</div>
							</div>
						</div>
					</div>
					<div id="feature-tab2" class="tab-pane fade feature-tab-styles">
						<div class="row">
							<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
								<div class="features-content-text">
									<h1>Gestiona <span class="hidden-xs"><br /></span> tus inmuebles</h1>
									<p>Crea fichas de los inmuebles que tienes en oferta y <strong>gestiona leads y agentes relacionados con éstos.</strong></p>
									<p>Asigna a cada ficha de inmueble sus <strong>características, clasíficalos</strong> según tus necesidades y añade <strong>etiquetas de venta especiales,</strong> como “Oportunidad” u “Obra Nueva”.</p>
								</div>
							</div>
							<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
								<div class="feature-content-image">
									<img src="{{ Theme::url('/images/corporate/features/picture-tab-content-1.png') }}" class="img-responsive">
								</div>
							</div>
						</div>
					</div>
					<div id="feature-tab3" class="tab-pane fade feature-tab-styles">
						<div class="row">
							<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
								<div class="features-content-text">
									<h1>Controla las métricas <span class="hidden-xs"><br /></span> de tus agentes</h1>
									<p>Tendrás <strong>el control de todas las métricas de tus agentes</strong> inmobiliarios y podrás <strong>relacionar agentes con inmuebles,</strong> así como <strong>medir la eficacia</strong> de cada uno de tus agentes.</p>
								</div>
							</div>
							<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
								<div class="feature-content-image">
									<img src="{{ Theme::url('/images/corporate/features/picture-tab-content-1.png') }}" class="img-responsive">
								</div>
							</div>
						</div>
					</div>
					<div id="feature-tab4" class="tab-pane fade feature-tab-styles">
						<div class="row">
							<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
								<div class="features-content-text">
									<h1>Identifica leads y <span class="hidden-xs"><br /></span> relaciónalos</h1>
									<p>Molista además <strong>te sugiere inmuebles que podrían interesar a tus leads,</strong> basándose en sus criterios de búsqueda, que quedan <strong>almacenados en el sistema.</strong> Podrás generar <strong>más visitas y venderás más y más rápido.</strong></p>
								</div>
							</div>
							<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
								<div class="feature-content-image">
									<img src="{{ Theme::url('/images/corporate/features/picture-tab-content-1.png') }}" class="img-responsive">
								</div>
							</div>
						</div>
					</div>
					<div id="feature-tab5" class="tab-pane fade feature-tab-styles">
						<div class="row">
							<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
								<div class="features-content-text">
									<h1>Sincroniza tus anuncios <span class="hidden-xs"><br /></span> con los principales <span class="hidden-xs"><br /></span> portales inmobiliarios</h1>
									<br>
									<p>Usando el CRM de molista puedes automáticamente <strong>sincronizar los anuncios de tus inmuebles en diferentes portales inmobiliarios, como Idealista, Fotocasa o Pisos.com</strong></p>
								</div>
							</div>
							<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
								<div class="feature-content-image">
									<img src="{{ Theme::url('/images/corporate/features/picture-tab-content-5-1.png') }}" class="img-responsive">

									<img src="{{ Theme::url('/images/corporate/features/picture-tab-content-5-2.png') }}" class="img-responsive feature-tab-5-img-right">

									<img src="{{ Theme::url('/images/corporate/features/picture-tab-content-5-3.png') }}" class="img-responsive feature-tab-5-img-left">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		
		<div id="features-home-content">
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
		
			<!-- BLOCK LINKS -->
			<section class="block-links">
				<div class="container">
					<div class="row">
						<div class="col-lg-6 col-lg-offset-3 clearfix">
						  <ul>
					        <li><button class="btn btnBdrYlw text-uppercase">VER DEMO</button></li>
					        <li><button class="btn btnBdrYlw text-uppercase" data-toggle="modal" data-target="#contact-modal">Contactar</button></li>
					      </ul>
						</div>
					</div>
				</div>
			</section>
			<!--/ BLOCK LINKS -->

		</div>

	</div>

@endsection
