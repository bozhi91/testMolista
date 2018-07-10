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
	<link href="{{ Theme::url('/compiled/css/corporate.css').'?v='.env('CSS_VERSION') }}" rel="stylesheet" type='text/css' />

	@if ( LaravelLocalization::getCurrentLocaleDirection() == 'rtl' )
		<link href="{{ Theme::url('/compiled/css/rtl.css') }}" rel="stylesheet" type='text/css' />
	@endif

	@if ( env('WHITELABEL_CSS') )
		<link href="{{ env('WHITELABEL_CSS').'?v='.env('CSS_VERSION') }}" rel="stylesheet" type='text/css' />
	@endif

	<link id="page_favicon" href="{{ asset( env('WHITELABEL_FAVICON','favicon.ico') ) }}" rel="icon" type="image/x-icon" />

	@if (env('SENTRY_PUBLIC_DNS'))
	<script src="https://cdn.ravenjs.com/3.9.1/raven.min.js"></script>
	<script type="text/javascript">
		Raven.config('{{ env('SENTRY_PUBLIC_DNS') }}').install();
		@if (Auth::check())
		Raven.setUserContext({ id: '{{ Auth::user()->id }}' });
		@endif
	</script>
	@endif

	<script type="text/javascript">
		var ready_callbacks = [];
	</script>

	@if ( env('HIDE_CORPORATE_COOKIES_WARNING', false) )
		<style type="text/css">
			#hs-eu-cookie-confirmation { display: none !important; }
		</style>
	@endif

</head>

<body class="dir-{{ LaravelLocalization::getCurrentLocaleDirection() }} theme-{{ Theme::get() }}">

	@include('corporate.common.analytics')

	@if ( @$custom_header )
		@include($custom_header)
	@else
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
						<a class="navbar-brand" href="{{ @$corporate_links['home'] }}" title="{{ Lang::get('corporate/seo.header.link.home') }}">

							<!--
							<img src="{{ Theme::url( env('WHITELABEL_LOGO_HEADER', '/images/corporate/logo.png') ) }}" alt="{{ Lang::get('corporate/seo.header.image.logo') }}">
							-->
						</a>
					</div>

					<div class="collapse navbar-collapse navbar-right" id="bs-example-navbar-collapse-1">
						<ul class="nav navbar-nav">
							@if ( @$corporate_links['demo'] )
								<li><a href="{{ $corporate_links['demo'] }}" title="{{ Lang::get('corporate/seo.header.link.demo') }}" class="btn btnBdrYlw text-uppercase">{{ Lang::get('corporate/general.demo') }}</a></li>
							@endif
							@if ( @$corporate_links['features'] )
								<li><a href="{{ $corporate_links['features'] }}" title="{{ Lang::get('corporate/seo.header.link.features') }}" class="btn btnBdrYlw text-uppercase">{{ Lang::get('corporate/general.moreinfo') }}</a></li>
							@endif
							@if ( @$corporate_links['pricing'] )
								<li><a href="{{ $corporate_links['pricing'] }}" title="{{ Lang::get('corporate/seo.header.link.pricing') }}" class="btn btnBdrYlw text-uppercase">{{ Lang::get('corporate/general.pricing') }}</a></li>
							@endif
							@if ( false && @$enabled_locales && count($enabled_locales) > 1 )
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
							        <a class="corporate-header-tel" href="tel:{{ str_replace(' ', '', Config::get('app.phone_support')) }}">{{ Config::get('app.phone_support') }}</a>
							    </div>
							</li>
						</ul>
					</div>
				</div>
			</nav>
		</header>
	@endif

	<section class="section-content">
		@yield('content')
	</section>

	<!-- FOOTER -->
	<footer>
		<div class="container">
			<div class="row">
				<div class="col-md-12 text-center">
					<ul class="footer-menu list-inline">
						@if ( @$corporate_links['support'] )
							<li class="text-nowrap"><a href="{{ $corporate_links['support'] }}" data-toggle="modal" rel="nofollow" title="{{ Lang::get('corporate/seo.footer.link.support') }}">{{ Lang::get('corporate/general.support') }}</a></li>
							<li class="hidden-xs">I</li>
						@endif

						@if ( @$corporate_links['contact'] )
							<li class="text-nowrap"><a href="{{ $corporate_links['contact'] }}" rel="nofollow" title="{{ Lang::get('corporate/seo.footer.link.contact') }}" data-toggle="modal">{{ Lang::get('corporate/general.contact') }}</a></li>
							<li class="hidden-xs">I</li>
						@endif

						@if ( @$corporate_links['legal'] )
							<li class="text-nowrap"><a href="{{ $corporate_links['legal'] }}" title="{{ Lang::get('corporate/seo.footer.link.legal') }}">{{ Lang::get('corporate/home.footer.legal') }}</a></li>
							<li class="hidden-xs">I</li>
						@endif

						@if ( @$corporate_links['privacy'] )
							<li class="text-nowrap"><a href="{{ $corporate_links['privacy'] }}" title="{{ Lang::get('corporate/seo.footer.link.privacy') }}">{{ Lang::get('corporate/home.footer.privacy') }}</a></li>
							<li class="hidden-xs">I</li>
						@endif

						@if ( @$corporate_links['cookies'] )
							<li class="text-nowrap"><a href="{{ $corporate_links['cookies'] }}" title="{{ Lang::get('corporate/seo.footer.link.cookies') }}">{{ Lang::get('corporate/home.footer.cookies') }}</a></li>
							<li class="hidden-xs">I</li>
						@endif

						<li class="text-nowrap"><a href="{{ action('Corporate\CustomersController@getIndex') }}" title="{{ Lang::get('corporate/seo.footer.link.customer') }}">{{ Lang::get('corporate/home.footer.admin.access') }}</a></li>
					</ul>
					<div class="footer-text">
						<strong>{{ env('WHITELABEL_WEB_URL','molista.com') }}</strong> {{ Lang::get('corporate/home.footer.operated') }} <strong><a href="{{ env('WHITELABEL_OWNER_URL','http://www.incubout.com/') }}" target="_blank" title="{{ env('WHITELABEL_WEB_URL','molista.com') }} {{ Lang::get('corporate/home.footer.operated') }} {{ env('WHITELABEL_OWNER_NAME','Incubout SL') }}">{{ env('WHITELABEL_OWNER_NAME','Incubout SL') }}</a></strong>:
						<div class="visible-xs"></div>
						<span class="text-nowrap"> {{ env('WHITELABEL_OWNER_ADDRESS','Salvador Espriu 93 08005 Barcelona') }}</span>
						<div class="visible-xs"></div>
						<span class="text-nowrap"> T: <strong><a href="tel:{{ str_replace(' ', '', Config::get('app.phone_support')) }}">{{ Config::get('app.phone_support') }}</a></strong></span>
						<div class="visible-xs"></div>
						<span class="text-nowrap"> E: <strong><a href="mailto:{{ env('MAIL_CONTACT','info@molista.com') }}" target="_blank">{{ env('MAIL_CONTACT','info@molista.com') }}</a></strong></span>
					</div>
				</div>
			</div>
		</div>
	</footer>
	<!-- / FOOTER -->

	@include('common.contact-modal')

	@if ( ! env('HIDE_CORPORATE_COOKIES_WARNING', false) )
		@include('common.cookies-warning')
	@endif

	<script src="{{ Theme::url('/compiled/js/corporate.js').'?v='.env('JS_VERSION') }}"></script>
	<script src="{{ Theme::url('/js/jquery.validate/messages_' . LaravelLocalization::getCurrentLocale() . '.min.js') }}"></script>
	<script src="{{ Theme::url('/js/alertify/messages_' . LaravelLocalization::getCurrentLocale() . '.js') }}"></script>

	@include('corporate.common.zopim')
	@include('corporate.common.leadin')

	@if ( @$show_signup_adwords_tracker )
		@include('corporate.common.signup_adwords_tracker')
	@endif

	@include('common.hubspot')

</body>
</html>
