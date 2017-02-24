<!-- FIVE BLOCK -->
<section class="five-block">
	<div class="container">
		<div class="row">
			<div class="col-sm-2 text-center">
				<img src="{{ asset("images/corporate/sello.png") }}" />
			</div>
			<div class="title-block col-sm-10 text-center">
				<h3>{{ Lang::get('corporate/home.distributor.title') }}</h3>
				<p class="col-sm-8 col-sm-offset-2">{{ Lang::get('corporate/home.distributor.description') }}</p>

				<a href="{{ action('Corporate\DistribuitorController@getIndex') }}" class="btn text-uppercase btn-distributor" 
				   title="{{ Lang::get('corporate/home.distributor.title') }}">{{ Lang::get('corporate/home.distributor.try') }}</a>
			</div>
		</div>
	</div>
</section>
<!-- / FIVE BLOCK -->