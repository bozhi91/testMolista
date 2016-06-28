<section id="header-top">
	<div class="container">
		<div class="">
			<ul class="nav navbar-nav pull-right">
				@if ( !empty($site_setup['locales_select']) )
					<li class="dropdown locale-select">
						<a href="#" class="main-item dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
							<span class="locale-placeholder">
								{{ Lang::get('web/header.lang') }} <span class="caret"></span>
								<span class="locale-line">
							</span> 
						</a>
						<ul class="dropdown-menu">
							@foreach($site_setup['locales_select'] as $locale => $locale_name)
								<li><a href="{{ ( $locale == LaravelLocalization::getCurrentLocale() ) ? 'javascript:;' : LaravelLocalization::getLocalizedURL($locale) }}">{{ $locale_name }}</a></li>
							@endforeach
						</ul>
					</li>
				@endif
				@if ( !empty($site_setup['social_media']) )
					<li class="social-media">
						<ul class="list-inline">
							@foreach ($site_setup['social_media'] as $key=>$value)
								<li>
									<a href="{{ $value }}" class="social-media-item" target="_blank">
										<i class="fa fa-{{$key}}" aria-hidden="true"></i>
									</a>
								</li>
							@endforeach
						</ul>
					</li>
				@endif
			</ul>
		</div>
	</div>
</section>

<nav id="header" class="navbar navbar-default {{ @$header_class }}">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
				<span class="sr-only">Toggle Navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="{{ action('WebController@index') }}" style="background-image: url('{{ empty($site_setup['logo']) ? Theme::url('/images/logo.png') : $site_setup['logo'] }}');"></a>
		</div>

		<div class="collapse navbar-collapse" id="app-navbar-collapse">
			@if ( !empty($site_setup['widgets']['header']) )
				@foreach ($site_setup['widgets']['header'] as $widget)
					@if ( $widget->type == 'menu' )
						@if ( $widget->menu )
							<ul class="nav navbar-nav navbar-right">
								@foreach ($widget->menu->items as $item)
									<li>
										<a href="{{ $item->item_url }}" target="{{ $item->target }}" class="main-item {{ (rtrim(url()->current(),'/') == $item->item_url) ? 'current' : '' }}">
											{{ $item->item_title }}
										</a>
									</li>
								@endforeach
							</ul>
						@endif
					@endif
				@endforeach
			@endif
		</div>
	</div>
</nav>

<script type="text/javascript">
	ready_callbacks.push(function() {
		var cont = $('#header');
	});
</script>
