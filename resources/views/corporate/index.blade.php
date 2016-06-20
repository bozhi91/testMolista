@extends('layouts.corporate')

@section('content')

	<div id="home" class="home">

			<!-- BANNER -->
			<div class="jumbotron">
			  <div class="container">
			  	<div class="col-md-6">
				    <h1>{{ Lang::get('corporate/home.h1') }}</h1>
				    {!! Lang::get('corporate/home.intro') !!}
			    </div>
			  </div>
			</div>
			<!-- / BANNER -->

			<!-- FIRST BLOCK -->
			<section class="first-block">
				<div class="container">
					<div class="row">
						<div class="col-md-6">
							<h2>{{ Lang::get('corporate/home.h2') }}</h2>
							{!! Lang::get('corporate/home.features') !!}
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
					        <li><a href="http://demo.molista.com/" target="_blank" class="btn btnBdrYlw text-uppercase">{{ Lang::get('corporate/general.demo') }}</a></li>
					        <li><a href="{{ action('Corporate\FeaturesController@getIndex') }}" class="btn btnBdrYlw text-uppercase">{{ Lang::get('corporate/general.moreinfo') }}</a></li>
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

			<!-- FOURTH BLOCK -->
			<section class="fourth-block">
				<div class="container">
					<div class="row">
						<div class="title-block col-md-6 col-md-offset-3 text-center">
						  <h3>{{ Lang::get('corporate/home.convinced.title') }}</h3>
						</div>
					</div>
				</div>
				<div class="jumbotron-bottom">
					<div class="container">
						<div class="row">
							<div class="col-sm-4 col-sm-offset-2 col-md-3 col-md-offset-3 text-right">
								{!! Lang::get('corporate/home.convinced.demo') !!}
							</div>
							<div class="col-sm-4 col-md-3 text-left">
								{!! Lang::get('corporate/home.convinced.test') !!}
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
					        <li><a href="http://demo.molista.com/" target="_blank" class="btn btnBdrYlw text-uppercase">{{ Lang::get('corporate/general.demo') }}</a></li>
					        <li><a href="{{ action('Corporate\FeaturesController@getIndex') }}" class="btn btnBdrYlw text-uppercase" >{{ Lang::get('corporate/general.moreinfo') }}</a></li>
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
