@extends('layouts.corporate')

@section('content')

	<div id="features-banner">
		<div class="features-banner-padding">
			<h1>{{ Lang::get('corporate/features.characteristics') }}</h1>
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
										<p>{{ Lang::get('corporate/features.web.link') }}</p>
									</div>
								</a>
							</li>
							<li>
								<a data-toggle="tab" href="#feature-tab2">
									<div class="features-tab-selector-image">
										<img src="{{ Theme::url('/images/corporate/features/icon-tab-2.png') }}" class="img-responsive">
									</div>
									<div class="features-tab-selector-text hidden-xs">
										<p>{{ Lang::get('corporate/features.property.link') }}</p>
									</div>
								</a>
							</li>
							<li>
								<a data-toggle="tab" href="#feature-tab3">
									<div class="features-tab-selector-image">
										<img src="{{ Theme::url('/images/corporate/features/icon-tab-3.png') }}" class="img-responsive">
									</div>
									<div class="features-tab-selector-text hidden-xs">
										<p>{{ Lang::get('corporate/features.agents.link') }}</p>
									</div>
								</a>
							</li>
							<li>
								<a data-toggle="tab" href="#feature-tab4">
									<div class="features-tab-selector-image">
										<img src="{{ Theme::url('/images/corporate/features/icon-tab-4.png') }}" class="img-responsive">
									</div>
									<div class="features-tab-selector-text hidden-xs">
										<p>{{ Lang::get('corporate/features.leads.link') }}</p>
									</div>
								</a>
							</li>
							<li>
								<a data-toggle="tab" href="#feature-tab5">
									<div class="features-tab-selector-image">
										<img src="{{ Theme::url('/images/corporate/features/icon-tab-5.png') }}" class="img-responsive">
									</div>
									<div class="features-tab-selector-text hidden-xs">
										<p>{{ Lang::get('corporate/features.integrations.link') }}</p>
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
									<h1>{!! Lang::get('corporate/features.web.title') !!}</h1>
									{!! Lang::get('corporate/features.web.text') !!}
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
									<h1>{!! Lang::get('corporate/features.property.title') !!}</h1>
									{!! Lang::get('corporate/features.property.text') !!}
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
									<h1>{!! Lang::get('corporate/features.agents.title') !!}</h1>
									{!! Lang::get('corporate/features.agents.text') !!}
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
									<h1>{!! Lang::get('corporate/features.leads.title') !!}</h1>
									{!! Lang::get('corporate/features.leads.text') !!}
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
									<h1>{!! Lang::get('corporate/features.integrations.title') !!}</h1>
									{!! Lang::get('corporate/features.integrations.text') !!}
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
						  <h3>{{ Lang::get('corporate/features.features.title') }}</h3>
						</div>
					</div>
					<div class="row">
						<div class="col-md-10 col-md-offset-1">

						  	<div class="panel first-panel">
						  		<div class="row">
						  			<div class="col-md-4">
						  				<h4>{{ Lang::get('corporate/features.features.simple.title') }}<h4>
						  			</div>
						  			<div class="col-md-8">
						  				{!! Lang::get('corporate/features.features.simple.text') !!}
						  			</div>

						  		</div>
						  	</div>

						  	<div class="panel second-panel">
						  		<div class="row">
						  			<div class="col-md-4">
						  				<h4>{{ Lang::get('corporate/features.features.power.title') }}<h4>
						  			</div>
						  			<div class="col-md-8">
						  				{!! Lang::get('corporate/features.features.power.text') !!}
						  			</div>

						  		</div>
						  	</div>

						  	<div class="panel third-panel">
						  		<div class="row">
						  			<div class="col-md-4">
						  				<h4>{{ Lang::get('corporate/features.features.customizable.title') }}<h4>
						  			</div>
						  			<div class="col-md-8">
						  				{!! Lang::get('corporate/features.features.customizable.text') !!}
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
					        <li><button class="btn btnBdrYlw text-uppercase">{{ Lang::get('corporate/general.demo') }}</button></li>
					        <li><button class="btn btnBdrYlw text-uppercase" data-toggle="modal" data-target="#contact-modal">{{ Lang::get('corporate/general.contact') }}</button></li>
					      </ul>
						</div>
					</div>
				</div>
			</section>
			<!--/ BLOCK LINKS -->

		</div>

	</div>

@endsection
