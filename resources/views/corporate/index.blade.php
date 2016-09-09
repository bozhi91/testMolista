@extends('layouts.corporate')

@section('content')

	<div id="home" class="home">

			<!-- BANNER -->
			<div class="jumbotron">
			  <div class="container">
			  	<div class="col-md-6">
				    <h1>{{ Lang::get('corporate/home.h1') }}</h1>
				    {!! Lang::get('corporate/home.intro') !!}
				    <div class="btn-area">
				    	<a href="{{ action('Corporate\PricingController@getIndex') }}" title="{{ Lang::get('corporate/seo.home.link.try') }}" class="btn btn-try">{{ Lang::get('corporate/home.try') }}</a>
				    </div>
			    </div>
			  </div>
			</div>
			<!-- / BANNER -->

			<!-- FIRST BLOCK -->
			<section class="first-block">
				<div class="container">
					<div class="row">
						<div class="col-md-7">
							<h2>{{ Lang::get('corporate/home.h2') }}</h2>
							{!! Lang::get('corporate/home.features') !!}
							<div class="row">
								<div class="col-xs-12 col-sm-6">
									{!! Lang::get('corporate/home.features.column.left') !!}
								</div>
								<div class="col-xs-12 col-sm-5">
									{!! Lang::get('corporate/home.features.column.right') !!}
								</div>
							</div>
						</div>
						<div class="col-md-5">
							<img class="img-responsive" src="{{ Theme::url('/images/corporate/responsive.png') }}" alt="{{ Lang::get('corporate/seo.home.image.responsive') }}" />
						</div>

					</div>
				</div>
			</section>
			<!-- / FIRST BLOCK -->

			<?php
				$logos = \App\Session\Geolocation::get('config.marketplaces_images');
				$logos_folder = \App\Session\Geolocation::get('config.items_folder');
				if ( empty($logos) )
				{
					$logos = [ 'pisos.png','trovit.png','idealista.png','casinuevo.png','kyero.png','enalquiler.png','divendo.png' ];
					$logos_alt = [ 'pisos.com','trovit.es','idealista.com','casinuevo.es','kyero.com','enalquiler.com','divendo.es' ];
					$logos_folder = 'images/corporate/marketplaces';
				}
			?>
			<section class="block-exports text-center">
				<div class="container">
					<ul class="logos hidden-xs list-inline">
						@foreach ($logos as $key => $logo)
							<li><img src="{{ asset("{$logos_folder}/{$logo}") }}" alt="{{ @$logos_alt[$key] }}" /></li>
						@endforeach
					</ul>
					<ul class="logos visible-xs list-unstyled">
						@foreach ($logos as $logo)
							<li><img src="{{ asset("{$logos_folder}/{$logo}") }}" alt="{{ @$logos_alt[$key] }}" /></li>
						@endforeach
					</ul>
					<div class="and-more">
						{!! Lang::get('corporate/home.exports.more') !!}
					</div>
				</div>
			</section>

			<!-- SECOND BLOCK -->
			<!-- BLOCK LINKS -->
			<section class="block-links">
				<div class="container">
					<div class="row">
						<div class="col-xs-12 text-center">
						  <ul>
					        <li><a href="{{ action('Corporate\DemoController@getIndex') }}" title="{{ Lang::get('corporate/seo.header.link.demo') }}" class="btn btnBdrYlw text-uppercase">{{ Lang::get('corporate/general.demo') }}</a></li>
					        <li><a href="{{ action('Corporate\FeaturesController@getIndex') }}" title="{{ Lang::get('corporate/seo.header.link.features') }}" class="btn btnBdrYlw text-uppercase">{{ Lang::get('corporate/general.moreinfo') }}</a></li>
					        <li><a href="{{ action('Corporate\SignupController@getIndex') }}" title="{{ Lang::get('corporate/seo.header.link.pricing') }}" class="btn btnBdrYlw text-uppercase">{{ Lang::get('corporate/home.try') }}</a></li>
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
							<h3>{{ Lang::get('corporate/home.h3') }}</h3>
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

			@include('corporate.common.home-fourth-block')

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
			var cont = $('#home');

		});
	</script>

@endsection
