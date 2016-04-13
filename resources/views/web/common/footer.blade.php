<footer id="footer">
	<div class="container">
		<div class="footer-padder">
			<div class="highlights hidden-xs">
				<div class="row">
					<div class="col-xs-4">
						<h4>Lo más buscado</h4>
						<ul class="list-unstyled">
							<li>Lorem ipsum dolor</li>
							<li>Sit amet, consectetur adipisicing</li>
							<li>Tempor incididunt ut labore</li>
							<li>Duis aute irure dolor in reprehenderit</li>
							<li>Excepteur sint occaecat</li>
						</ul>
					</div>
					<div class="col-xs-4">
						<h4>Novedades</h4>
						<ul class="list-unstyled">
							<li>Lorem ipsum dolor</li>
							<li>Sit amet, consectetur adipisicing</li>
							<li>Tempor incididunt ut labore</li>
							<li>Duis aute irure dolor in reprehenderit</li>
							<li>Excepteur sint occaecat</li>
						</ul>
					</div>
					<div class="col-xs-4">
						<h4>Agencias</h4>
						<ul class="list-unstyled">
							<li>Lorem ipsum dolor</li>
							<li>Sit amet, consectetur adipisicing</li>
							<li>Tempor incididunt ut labore</li>
							<li>Duis aute irure dolor in reprehenderit</li>
							<li>Excepteur sint occaecat</li>
						</ul>
					</div>
				</div>
			</div>
			<div class="powered-by">Powered by</div>
			<ul class="nav navbar-nav quick-links">
				<li><img src="{{ Theme::url('/images/footer-logo.png') }}" alt="" /></li>
				<li class="big-link"><a href="{{ LaravelLocalization::getLocalizedURL(null, action('WebController@index')) }}">Publica tu anuncio</a></li>
				<li class="big-link"><a href="{{ LaravelLocalization::getLocalizedURL(null, action('WebController@index')) }}">Sobre nosotros</a></li>
				<li class="big-link"><a href="{{ LaravelLocalization::getLocalizedURL(null, action('WebController@index')) }}">Contactar</a></li>
				@if ( !empty($site_setup['social_media']) )
					<li>
						@foreach ($site_setup['social_media'] as $key=>$value)
							<a href="{{$value}}" target="_blank" class="social-link"><i class="fa fa-{{$key}}{{ in_array($key, [ 'facebook','twitter' ]) ? '-square' : '' }}" aria-hidden="true"></i></a>
						@endforeach
					</li>
				@endif
				<li class="pull-right">
					@if ( Auth::guest() )
						<a href="{{ action('Auth\AuthController@login') }}">{{ Lang::get('web/footer.login') }}</a>
					@else
						<a href="{{ action('Auth\AuthController@logout') }}">{{ Lang::get('web/footer.logout') }}</a>
					@endif
				</li>
			</ul>
			<div class="clearfix"></div>
		</div>
	</div>
</footer>

<script type="text/javascript">
	ready_callbacks.push(function(){
		var h = $('#footer').height();
		$('#sticky-wrapper').css({
			'margin-bottom': (-1*h)+'px',
			'padding-bottom': (h+20)+'px'
		});
	});
</script>
