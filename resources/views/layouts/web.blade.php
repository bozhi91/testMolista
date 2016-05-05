<!DOCTYPE html>
<html lang="{{ LaravelLocalization::getCurrentLocale() }}">

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
		<title>Molista</title>
	@endif

	@if ( !empty($seo_description) )
		<meta name="description" content="{{ $seo_description }}" />
	@elseif ( !empty($site_setup['seo']['description'][LaravelLocalization::getCurrentLocale()]) )
		<meta name="description" content="{{ $site_setup['seo']['description'][LaravelLocalization::getCurrentLocale()] }}" />
	@endif

	@if ( !empty($seo_keywords) )
		<meta name="keywords" content="{{ $seo_keywords }}" />
	@endif

	<link href="https://fonts.googleapis.com/css?family=Lato:400,300,700,900,300italic,400italic,700italic" rel="stylesheet" type="text/css" />
	<link href="{{ Theme::url('/compiled/css/app.css') }}" rel="stylesheet" type='text/css' />

	@if ( !empty($site_setup['favicon']) )
		<link id="page_favicon" href="{{ $site_setup['favicon'] }}" rel="icon" type="image/x-icon" />
	@else
		<link id="page_favicon" href="{{ asset('favicon.ico') }}" rel="icon" type="image/x-icon" />
	@endif

	<script src="http://maps.google.com/maps/api/js?key={{ Config::get('app.google_maps_api_key')}}"></script>

	<script type="text/javascript">
	var ready_callbacks = [];
	</script>

</head>

<body id="{{ @$body_id }}">

	<div id="sticky-wrapper" class="if-overlay-then-blurred">

		@include('web.common.header')

		@yield('content')

	</div>

	@include('web.common.footer')

	@if ( empty($hide_advanced_search_modal) )
		@include('web.search.modal')
	@endif

	<script src="{{ Theme::url('/compiled/js/app.js') }}"></script>
	<script src="{{ Theme::url('/js/jquery.validate/messages_' . LaravelLocalization::getCurrentLocale() . '.min.js') }}"></script>
	<script src="{{ Theme::url('/js/alertify/messages_' . LaravelLocalization::getCurrentLocale() . '.js') }}"></script>
	<script src="{{ Theme::url('/js/summernote/summernote-' . summetime_lang() . '.js') }}"></script>
	<script src="{{ Theme::url('/js/bootstrap-table/bootstrap-table-' . summetime_lang() . '.js') }}"></script>

</body>
</html>
