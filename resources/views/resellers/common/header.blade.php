<header id="header">
	<nav class="navbar navbar-default">
		<div class="container">

			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="/" title="{{ Lang::get('corporate/seo.header.link.home') }}">
					<img src="{{ Theme::url('/images/corporate/logo.png') }}" alt="{{ Lang::get('corporate/seo.header.image.logo') }}">
				</a>
			</div>

			<div class="collapse navbar-collapse navbar-right" id="bs-example-navbar-collapse-1">
				<ul class="nav navbar-nav">
					@if ( @$enabled_locales && count($enabled_locales) > 1 )
						<li class="language-container dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ (1==2) ? Lang::get('corporate/general.languages') : LaravelLocalization::getCurrentLocaleNative() }} <span class="caret"></span></a>
							<ul class="language_bar_chooser dropdown-menu">
								@foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
									@if ( in_array($localeCode, $enabled_locales) && $localeCode != LaravelLocalization::getCurrentLocale() )
										<li>
											<a rel="alternate" hreflang="{{$localeCode}}" href="{{LaravelLocalization::getLocalizedURL($localeCode) }}">
												{{{ $properties['native'] }}}
											</a>
										</li>
									@endif
								@endforeach
							</ul>
						</li>
					@endif
					@if ( @$reseller_user )
						<li><a href="{{ action('Resellers\AuthController@getLogout') }}" title="{{ Lang::get('corporate/seo.header.link.pricing') }}" class="btn btnBdrYlw text-uppercase">{{ Lang::get('resellers.logout') }}</a></li>
					@endif
				</ul>
			</div>

		</div>
	</nav>
</header>
