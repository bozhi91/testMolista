<?php 
	$enabled_locales = \App\Models\Locale::getCorporateLocales();
?>
<!DOCTYPE html>
<html lang="{{ LaravelLocalization::getCurrentLocale() }}" dir="{{ LaravelLocalization::getCurrentLocaleDirection() }}">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	@if ( !empty($seo_title) )
		<title>{{ $seo_title }}</title>
	@else
		<title>{{ env('WHITELABEL_WEBNAME', 'Molista') }}</title>
	@endif

	@if ( !empty($seo_description) )
		<meta name="description" content="{{ $seo_description }}" />
	@endif

	<link href="https://fonts.googleapis.com/css?family=Lato:400,300,700,900,300italic,400italic,700italic|Dosis:400,700,600,500" rel="stylesheet" type="text/css" />
	<link href="{{ Theme::url('/compiled/css/corporate.css') }}" rel="stylesheet" type='text/css' />

	@if ( LaravelLocalization::getCurrentLocaleDirection() == 'rtl' )
		<link href="{{ Theme::url('/compiled/css/rtl.css') }}" rel="stylesheet" type='text/css' />
	@endif

	@if ( env('WHITELABEL_CSS') )
		<link href="{{ env('WHITELABEL_CSS') }}" rel="stylesheet" type='text/css' />
	@endif

	<link id="page_favicon" href="{{ asset('favicon.ico') }}" rel="icon" type="image/x-icon" />

	<script type="text/javascript">
		var ready_callbacks = [];
	</script>

</head>

<body class="dir-{{ LaravelLocalization::getCurrentLocaleDirection() }}">

	@include('corporate.common.analytics')

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
						<img src="{{ Theme::url( env('WHITELABEL_LOGO_HEADER', '/images/corporate/logo.png') ) }}" alt="{{ Lang::get('corporate/seo.header.image.logo') }}">
					</a>
				</div>

				<div class="collapse navbar-collapse navbar-right" id="bs-example-navbar-collapse-1">
					<ul class="nav navbar-nav">
						<li><a href="{{ action('Corporate\DemoController@getIndex') }}" title="{{ Lang::get('corporate/seo.header.link.demo') }}" class="btn btnBdrYlw text-uppercase">{{ Lang::get('corporate/general.demo') }}</a></li>
						<li><a href="{{ action('Corporate\FeaturesController@getIndex') }}" title="{{ Lang::get('corporate/seo.header.link.features') }}" class="btn btnBdrYlw text-uppercase">{{ Lang::get('corporate/general.moreinfo') }}</a></li> 
						<li><a href="{{ action('Corporate\PricingController@getIndex') }}" title="{{ Lang::get('corporate/seo.header.link.pricing') }}" class="btn btnBdrYlw text-uppercase">{{ Lang::get('corporate/general.pricing') }}</a></li> 
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
						<li>
							<div class="phone-info">
								<i class="fa fa-phone" aria-hidden="true"></i>
								{{ Config::get('app.phone_support') }}
							</div>
						</li>
					</ul>
				</div>
			</div>
		</nav>
	</header>

	@yield('content')

	<!-- FOOTER -->
	<footer>
		<div class="container">
			<div class="row">
				<div class="col-md-12 text-center">
					<ul class="footer-menu list-inline">
						<li class="text-nowrap"><a href="#contact-modal" data-toggle="modal" rel="nofollow" title="{{ Lang::get('corporate/seo.footer.link.support') }}">{{ Lang::get('corporate/general.support') }}</a></li>
						<li class="hidden-xs">I</li>
						<li class="text-nowrap"><a href="#contact-modal" rel="nofollow" title="{{ Lang::get('corporate/seo.footer.link.contact') }}" data-toggle="modal">{{ Lang::get('corporate/general.contact') }}</a></li>
						<li class="hidden-xs">I</li>
						<li class="text-nowrap"><a href="{{ action('Corporate\InfoController@getLegal') }}" title="{{ Lang::get('corporate/seo.footer.link.legal') }}">{{ Lang::get('corporate/home.footer.legal') }}</a></li>
						<li class="hidden-xs">I</li>
						<li class="text-nowrap"><a href="{{ action('Corporate\InfoController@getLegal') }}#privacy-policy" title="{{ Lang::get('corporate/seo.footer.link.privacy') }}">{{ Lang::get('corporate/home.footer.privacy') }}</a></li>
						<li class="hidden-xs">I</li>
						<li class="text-nowrap"><a href="{{ action('Corporate\InfoController@getLegal') }}#cookies-policy" title="{{ Lang::get('corporate/seo.footer.link.cookies') }}">{{ Lang::get('corporate/home.footer.cookies') }}</a></li>
						<li class="hidden-xs">I</li>
						<li class="text-nowrap"><a href="{{ action('Corporate\CustomersController@getIndex') }}" title="{{ Lang::get('corporate/seo.footer.link.customer') }}">{{ Lang::get('corporate/home.footer.admin.access') }}</a></li>
					</ul>
					<div class="footer-text">
						<strong>molista.com</strong> {{ Lang::get('corporate/home.footer.operated') }} <strong><a href="http://www.incubout.com/" target="_blank" title="molista.com {{ Lang::get('corporate/home.footer.operated') }} Incubout SL">Incubout SL</a></strong>: 
						<div class="visible-xs"></div>
						<span class="text-nowrap">Salvador Espriu 93 08005 Barcelona</span>
						<div class="visible-xs"></div>
						<span class="text-nowrap">T: <strong>{{ Config::get('app.phone_support') }}</strong></span>
						<div class="visible-xs"></div>
						<span class="text-nowrap">E: <strong><a href="mailto:info@molista.com" target="_blank">info@molista.com</a></strong></span>
					</div>
				</div>
			</div>
		</div>
	</footer>
	<!-- / FOOTER -->

	@include('common.contact-modal')
	@include('common.cookies-warning')

	<script src="{{ Theme::url('/compiled/js/corporate.js') }}"></script>
	<script src="{{ Theme::url('/js/jquery.validate/messages_' . LaravelLocalization::getCurrentLocale() . '.min.js') }}"></script>
	<script src="{{ Theme::url('/js/alertify/messages_' . LaravelLocalization::getCurrentLocale() . '.js') }}"></script>

	@include('corporate.common.zopim')
	@include('corporate.common.leadin')

	@if ( @$show_signup_adwords_tracker )
		@include('corporate.common.signup_adwords_tracker')
	@endif

</body>
</html>
