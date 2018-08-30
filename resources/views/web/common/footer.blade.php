<footer id="footer" class="if-overlay-then-blurred">

	<div class="highlights">
		<div class="container">
			<div class="row">
				@if ( !empty($site_setup['widgets']['footer']) )
					@foreach ($site_setup['widgets']['footer'] as $widget)
						<div class="col-xs-12 col-md-4">
							@if ( $widget['type'] == 'menu' )
								<h4>{{ $widget['title'] }}</h4>
								@include('common.widget-menu', [
									'widget' => $widget,
									'widget_class' => 'list-unstyled',
								])
							@elseif ( $widget['type'] == 'text' )
								<h4>{{ $widget['title'] }}</h4>
								@include('common.widget-text', [
									'widget' => $widget,
								])
							@elseif ( $widget['type'] == 'awesome-link' )
								@if ($widget['title'])
								<h4>{{ $widget['title'] }}</h4>
								@endif
								@include('common.widget-awesome-link-footer', [
									'widget' => $widget,
								])
							@endif
							<div class="visible-xs-block visible-sm-block">&nbsp;</div>
						</div>
					@endforeach
				@endif
			</div>
		</div>
	</div>

	<div class="bottoms">
		<div class="container">
			<div class="row">
				<div class="col-xs-12">
					@if ( @$current_site && !$current_site->hide_Contromia )
						<div class="powered-by">Powered by</div>
					@endif
					<ul class="nav navbar-nav quick-links pull-right">
						<li class="pull-right">
							@if ( Auth::guest() )
								<a href="{{ action('Auth\AuthController@login') }}">{{ Lang::get('web/footer.login') }}</a>
							@else
								Admin:
								<a href="{{ action('AccountController@index') }}">home</a>
								|
								<a href="{{ action('Auth\AuthController@logout') }}">logout</a>
							@endif
						</li>
					</ul>
					@if ( @$current_site && !$current_site->hide_Contromia )
						<ul class="nav navbar-nav quick-links">
							<li><a href="{{ Config::get('app.application_url') }}" target="_blank"><img src="{{ Theme::url( env('WHITELABEL_LOGO_FOOTER', '/images/footer-logo.png') ) }}" alt="" /></a></li>
							@if ( !empty($site_setup['social_media']) )
								<li>
									@foreach ($site_setup['social_media'] as $key=>$value)
										<a href="{{$value}}" target="_blank" class="social-link"><i class="fa fa-{{$key}}{{ in_array($key, [ 'facebook','twitter' ]) ? '-square' : '' }}" aria-hidden="true"></i></a>
									@endforeach
								</li>
							@endif
						</ul>
					@endif
				</div>
			</div>
		</div>
	</div>

</footer>

<script type="text/javascript">
	ready_callbacks.push(function(){

		function onResize() {
			var h = $('#footer').height();
			$('#sticky-wrapper').css({
				'margin-bottom': (-1*h)+'px',
				'padding-bottom': (h)+'px'
			});
		}

		$(window).resize(onResize);
		onResize();

	});
</script>
