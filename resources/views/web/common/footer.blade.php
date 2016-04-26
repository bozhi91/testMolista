<footer id="footer">
	<div class="container">
		<div class="footer-padder">
			<div class="highlights hidden-xs">
				<div class="row">
					@if ( !empty($site_setup['widgets']['footer']) )
						@foreach ($site_setup['widgets']['footer'] as $widget)
							<div class="col-xs-4">
								@if ( $widget->type == 'menu' )
									<h4>{{ $widget->title }}</h4>
									<ul class="list-unstyled">
										@foreach ($widget->menu->items as $item)
											<li><a href="{{ $item->item_url }}">{{ $item->item_title }}</a></li>
										@endforeach
									</ul>
								@endif
							</div>
						@endforeach
					@endif
				</div>
			</div>
			<div class="powered-by">Powered by</div>
			<ul class="nav navbar-nav quick-links pull-right">
				<li class="pull-right">
					@if ( Auth::guest() )
						<a href="{{ action('Auth\AuthController@login') }}">{{ Lang::get('web/footer.login') }}</a>
					@else
						<a href="{{ action('Auth\AuthController@logout') }}">{{ Lang::get('web/footer.logout') }}</a>
					@endif
				</li>
			</ul>
			<ul class="nav navbar-nav quick-links">
				<li><img src="{{ Theme::url('/images/footer-logo.png') }}" alt="" /></li>
				@if ( !empty($site_setup['social_media']) )
					<li>
						@foreach ($site_setup['social_media'] as $key=>$value)
							<a href="{{$value}}" target="_blank" class="social-link"><i class="fa fa-{{$key}}{{ in_array($key, [ 'facebook','twitter' ]) ? '-square' : '' }}" aria-hidden="true"></i></a>
						@endforeach
					</li>
				@endif
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
			'padding-bottom': (h)+'px'
		});
	});
</script>
