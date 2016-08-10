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
			<div class="col-sm-12 text-center">
			  <ul>
		        <li><a href="{{ empty($demo_link) ? action('Corporate\DemoController@getIndex') : $demo_link }}" {{ empty($demo_target) ? '' : "target=\"$demo_target\"" }}  title="{{ Lang::get('corporate/seo.header.link.demo') }}" class="btn btnBdrYlw text-uppercase">{{ Lang::get('corporate/general.demo') }}</a></li>
		        <li><a href="{{ action('Corporate\FeaturesController@getIndex') }}" class="btn btnBdrYlw text-uppercase" title="{{ Lang::get('corporate/seo.header.link.features') }}">{{ Lang::get('corporate/general.moreinfo') }}</a></li>
		        <li><a href="{{ action('Corporate\SignupController@getIndex') }}" class="btn btnBdrYlw text-uppercase" title="{{ Lang::get('corporate/seo.header.link.pricing') }}">{{ Lang::get('corporate/home.try') }}</a></li>
		      </ul>
			</div>
		</div>
	</div>
</section>
<!--/ BLOCK LINKS -->