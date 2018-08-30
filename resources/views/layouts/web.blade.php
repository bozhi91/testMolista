<!DOCTYPE html>
<html lang="{{ LaravelLocalization::getCurrentLocale() }}" dir="{{ LaravelLocalization::getCurrentLocaleDirection() }}">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	@if ( !empty($seo_title) )
		<title>{{ $seo_title }}</title>
	@elseif ( !empty($site_setup['seo']['title'][LaravelLocalization::getCurrentLocale()]) )
		<title>{{ $site_setup['seo']['title'][LaravelLocalization::getCurrentLocale()] }}</title>
	@elseif ( !empty($site_setup['seo']['title'][fallback_lang()]) )
		<title>{{ $site_setup['seo']['title'][fallback_lang()] }}</title>
	@else
		<title>{{ env('WHITELABEL_WEBNAME', 'Contromia') }}</title>
	@endif

	@if ( !empty($seo_description) )
		<meta name="description" content="{{ $seo_description }}" />
	@elseif ( !empty($site_setup['seo']['description'][LaravelLocalization::getCurrentLocale()]) )
		<meta name="description" content="{{ $site_setup['seo']['description'][LaravelLocalization::getCurrentLocale()] }}" />
	@endif

	@if ( !empty($seo_keywords) )
		<meta name="keywords" content="{{ $seo_keywords }}" />
	@endif

	@if ( !empty($fullcalendar_enabled) )
		<link href="{{ Theme::url('/css/fullcalendar.min.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ Theme::url('/css/fullcalendar.print.css') }}" rel="stylesheet" media="print" type="text/css" />
	@endif

	<link href="https://fonts.googleapis.com/css?family=Lato:400,300,700,900,300italic,400italic,700italic" rel="stylesheet" type="text/css" />
	<link href="{{ Theme::url('/compiled/css/app.css').'?v='.env('CSS_VERSION') }}" rel="stylesheet" type='text/css' />

	@if ( LaravelLocalization::getCurrentLocaleDirection() == 'rtl' )
		<link href="{{ Theme::url('/compiled/css/rtl.css') }}" rel="stylesheet" type='text/css' />
	@endif

	@if ( !empty($site_setup['favicon']) )
		<link id="page_favicon" href="{{ $site_setup['favicon'] }}" rel="icon" type="image/x-icon" />
	@else
		<link id="page_favicon" href="{{ asset( env('WHITELABEL_FAVICON', 'favicon.ico') ) }}" rel="icon" type="image/x-icon" />
	@endif

	@if ( !empty($use_google_maps) )
		<script src="//maps.google.com/maps/api/js?key={{ Config::get('app.google_maps_api_key')}}"></script>
	@endif

	@if (!empty($og))
		{!! $og->renderTags() !!}
	@endif

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

</head>

<body id="{{ @$body_id }}" class="dir-{{ LaravelLocalization::getCurrentLocaleDirection() }} theme-{{ Theme::get() }} {{ !empty($current_site) ? 'site-id-'.$current_site->id.' site-'.$current_site->subdomain : '' }}">

	<?php $ga_account = isset($google_analitics_account) ? $google_analitics_account : @$current_site->ga_account; ?>
	@if ( $ga_account )
		@include('common.analytics', [ 'ga_account' => $ga_account ])
	@endif

	<div id="sticky-wrapper" class="if-overlay-then-blurred">

		@include('web.common.header')

		@yield('content')

	</div>

	@include('web.common.footer')

	@if ( empty($hide_advanced_search_modal) )
		@include('web.search.modal')
	@endif

	@include('web.common.cookies', [ 'showjs'=>true ])

	@if ( Request::server('REQUEST_SCHEME') == 'https' || @$_SERVER['HTTPS'] == 'on' )
		<script type="text/javascript" src="https://ws.sharethis.com/button/buttons.js"></script>
	@else
		<script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>
	@endif
	<script type="text/javascript">
	if (typeof(stLight) !== "undefined") {
		stLight.options({
			publisher: "2572efa4-03fa-451c-b604-4fb0add8bbb4",
			doNotHash: true,
			doNotCopy: true,
			hashAddressBar: false,
			popup: true,
			servicePopup: true
		});
	}
	</script>

	<script src="{{ Theme::url('/compiled/js/app.js').'?v='.env('JS_VERSION') }}"></script>
	<script src="{{ Theme::url('/js/jquery.validate/messages_' . LaravelLocalization::getCurrentLocale() . '.min.js') }}"></script>
	<script src="{{ Theme::url('/js/alertify/messages_' . LaravelLocalization::getCurrentLocale() . '.js') }}"></script>
	<script src="{{ Theme::url('/js/summernote/summernote-' . summetime_lang() . '.js') }}"></script>
	<script src="{{ Theme::url('/js/bootstrap-table/bootstrap-table-' . summetime_lang() . '.js') }}"></script>

	@if ( !empty($fullcalendar_enabled) )
		<script src="{{ Theme::url('/js/fullcalendar.min.js') }}"></script>
		<script src="{{ Theme::url('/js/fullcalendar/' . LaravelLocalization::getCurrentLocale() . '.js') }}"></script>
	@endif

	<script type="text/javascript">
		for (var t=0; t<ready_callbacks.length; t++) {
			ready_callbacks[t]();
		}
	</script>

</body>
</html>
