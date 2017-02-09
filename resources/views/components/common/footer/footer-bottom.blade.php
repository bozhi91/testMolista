<div class="bottoms">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				@if ( @$current_site && !$current_site->hide_molista )
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
				@if ( @$current_site && !$current_site->hide_molista )
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