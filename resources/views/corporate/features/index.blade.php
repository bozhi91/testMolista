@extends('layouts.corporate')

@section('content')

	<div id="features-banner">
		<div class="features-banner-padding">
			@if ( $current_tab == 'tab1' )
				<h1>{{ Lang::get('corporate/features.characteristics') }}: {{ Lang::get('corporate/features.web.link') }}</h1>
			@elseif ( $current_tab == 'tab2' )
				<h1>{{ Lang::get('corporate/features.characteristics') }}: {{ Lang::get('corporate/features.property.link') }}</h1>
			@elseif ( $current_tab == 'tab3' )
				<h1>{{ Lang::get('corporate/features.characteristics') }}: {{ Lang::get('corporate/features.agents.link') }}</h1>
			@elseif ( $current_tab == 'tab4' )
				<h1>{{ Lang::get('corporate/features.characteristics') }}: {{ Lang::get('corporate/features.leads.link') }}</h1>
			@elseif ( $current_tab == 'tab5' )
				<h1>{{ Lang::get('corporate/features.characteristics') }}: {{ Lang::get('corporate/features.integrations.link') }}</h1>
			@else
				<h1>{{ Lang::get('corporate/features.characteristics') }}</h1>
			@endif
		</div>
	</div>

	<div id="features">

		<div id="features-tab-selector">
			<div class="container">
				<div class="row">
					<div class="features-tab-selector-block">
						<ul class="nav nav-tabs nav-justified">
							<li class="{{ $current_tab == 'tab1' ? 'active' : '' }}">
								<a href="{{ action('Corporate\FeaturesController@getIndex', $tab_options['tab1']) }}">
									<div class="features-tab-selector-image">
										<img src="{{ Theme::url('/images/corporate/features/icon-tab-1.png') }}" class="img-responsive">
									</div>
									<div class="features-tab-selector-text hidden-xs">
										<p>{{ Lang::get('corporate/features.web.link') }}</p>
									</div>
								</a>
							</li>
							<li class="{{ $current_tab == 'tab2' ? 'active' : '' }}">
								<a href="{{ action('Corporate\FeaturesController@getIndex', $tab_options['tab2']) }}">
									<div class="features-tab-selector-image">
										<img src="{{ Theme::url('/images/corporate/features/icon-tab-2.png') }}" class="img-responsive">
									</div>
									<div class="features-tab-selector-text hidden-xs">
										<p>{{ Lang::get('corporate/features.property.link') }}</p>
									</div>
								</a>
							</li>
							<li class="{{ $current_tab == 'tab3' ? 'active' : '' }}">
								<a href="{{ action('Corporate\FeaturesController@getIndex', $tab_options['tab3']) }}">
									<div class="features-tab-selector-image">
										<img src="{{ Theme::url('/images/corporate/features/icon-tab-3.png') }}" class="img-responsive">
									</div>
									<div class="features-tab-selector-text hidden-xs">
										<p>{{ Lang::get('corporate/features.agents.link') }}</p>
									</div>
								</a>
							</li>
							<li class="{{ $current_tab == 'tab4' ? 'active' : '' }}">
								<a href="{{ action('Corporate\FeaturesController@getIndex', $tab_options['tab4']) }}">
									<div class="features-tab-selector-image">
										<img src="{{ Theme::url('/images/corporate/features/icon-tab-4.png') }}" class="img-responsive">
									</div>
									<div class="features-tab-selector-text hidden-xs">
										<p>{{ Lang::get('corporate/features.leads.link') }}</p>
									</div>
								</a>
							</li>
							<li class="{{ $current_tab == 'tab5' ? 'active' : '' }}">
								<a href="{{ action('Corporate\FeaturesController@getIndex', $tab_options['tab5']) }}">
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
					@if ( $current_tab == 'tab1' )
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
					@elseif ( $current_tab == 'tab2' )
						<div id="feature-tab2" class="tab-pane fade in active feature-tab-styles">
							<div class="row">
								<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
									<div class="features-content-text">
										<h1>{!! Lang::get('corporate/features.property.title') !!}</h1>
										{!! Lang::get('corporate/features.property.text') !!}
									</div>
								</div>
								<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
									<div class="feature-content-image">
										<img src="{{ Theme::url('/images/corporate/features/picture-tab-content-2.png') }}" class="img-responsive">
									</div>
								</div>
							</div>
						</div>
					@elseif ( $current_tab == 'tab3' )
						<div id="feature-tab3" class="tab-pane fade in active feature-tab-styles">
							<div class="row">
								<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
									<div class="features-content-text">
										<h1>{!! Lang::get('corporate/features.agents.title') !!}</h1>
										{!! Lang::get('corporate/features.agents.text') !!}
									</div>
								</div>
								<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
									<div class="feature-content-image">
										<img src="{{ Theme::url('/images/corporate/features/picture-tab-content-3.png') }}" class="img-responsive">
									</div>
								</div>
							</div>
						</div>
					@elseif ( $current_tab == 'tab4' )
						<div id="feature-tab4" class="tab-pane fade in active feature-tab-styles">
							<div class="row">
								<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
									<div class="features-content-text">
										<h1>{!! Lang::get('corporate/features.leads.title') !!}</h1>
										{!! Lang::get('corporate/features.leads.text') !!}
									</div>
								</div>
								<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
									<div class="feature-content-image">
										<img src="{{ Theme::url('/images/corporate/features/picture-tab-content-4.png') }}" class="img-responsive">
									</div>
								</div>
							</div>
						</div>
					@elseif ( $current_tab == 'tab5' )
						<div id="feature-tab5" class="tab-pane fade in active feature-tab-styles">
							<div class="row">
								<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
									<div class="features-content-text">
										<h1>{!! Lang::get('corporate/features.integrations.title') !!}</h1>
										{!! Lang::get('corporate/features.integrations.text') !!}
									</div>
								</div>
								<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
									<div class="feature-content-image">
										@if ( \App\Session\Geolocation::get('config.feature_image') )
											<?php $config = \App\Session\Geolocation::get('config') ?>
											<img src="{{ Theme::url("{$config['items_folder']}/{$config['feature_image']}") }}" class="img-responsive">
										@else
											<img src="{{ Theme::url('/images/corporate/features/picture-tab-content-5.png') }}" class="img-responsive">
										@endif
									</div>
								</div>
							</div>
						</div>
					@endif
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
