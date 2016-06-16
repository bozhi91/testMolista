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
							<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
								<h1>Crea tu web <span class="hidden-xs"><br /></span> inmobiliaria</h1>
							</div>
							<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
								<p>Crea fichas de los inmuebles que tienes en oferta y <strong>gestiona leads y agentes relacionados con éstos.</strong></p>
								<p>Asigna a cada ficha de inmueble sus <strong>características, clasíficalos</strong> según tus necesidades y añade <strong>etiquetas de venta especiales,</strong> como “Oportunidad” u “Obra Nueva”.</p>
							</div>
							<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
								<div class="feature-content-image">
									<img class="img-responsive" src="">
								</div>
							</div>
						</div>
					</div>
					<div id="feature-tab2" class="tab-pane fade feature-tab-styles">
						<div class="row">
							<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6"></div>
							<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6"></div>
						<h1>Test 2</h1>
						</div>
					</div>
					<div id="feature-tab3" class="tab-pane fade feature-tab-styles">
						<div class="row">
							<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6"></div>
							<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6"></div>
							<h1>Test 3</h1>
						</div>
					</div>
					<div id="feature-tab4" class="tab-pane fade feature-tab-styles">
						<div class="row">
							<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6"></div>
							<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6"></div>
							<h1>Test 4</h1>
						</div>
					</div>
					<div id="feature-tab5" class="tab-pane fade feature-tab-styles">
						<div class="row">
							<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6"></div>
							<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6"></div>
							<h1>Test 5</h1>
						</div>
					</div>
				</div>
			</div>
		</div>



		<div class="container">
			<div class="row">
				<h1>TEST INFO</h1>
			</div>
		</div>


	</div>

@endsection
