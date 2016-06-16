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


		<div class="container">
			<div class="row">
				<h1>TEST INFO</h1>
			</div>
		</div>
	</div>

@endsection
