<!DOCTYPE html>
<html lang="{{ LaravelLocalization::getCurrentLocale() }}">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link href="https://fonts.googleapis.com/css?family=Lato:400,300,700,900,300italic,400italic,700italic" rel="stylesheet" type="text/css" />
	<link href="{{ Theme::url('/compiled/css/app.css').'?v='.env('CSS_VERSION') }}" rel="stylesheet" type='text/css' />

	@if ( !empty($use_google_maps) )
		<script src="http://maps.google.com/maps/api/js?key={{ Config::get('app.google_maps_api_key')}}"></script>
	@endif

	<script type="text/javascript">
		var ready_callbacks = [];
	</script>

</head>

<body id="body-popup">

	@yield('content')

	<script src="{{ Theme::url('/compiled/js/app.js').'?v='.env('JS_VERSION') }}"></script>
	<script src="{{ Theme::url('/js/jquery.validate/messages_' . LaravelLocalization::getCurrentLocale() . '.min.js') }}"></script>
	<script src="{{ Theme::url('/js/alertify/messages_' . LaravelLocalization::getCurrentLocale() . '.js') }}"></script>
	<script src="{{ Theme::url('/js/summernote/summernote-' . summetime_lang() . '.js') }}"></script>
	<script src="{{ Theme::url('/js/bootstrap-table/bootstrap-table-' . summetime_lang() . '.js') }}"></script>

	<script type="text/javascript">
		for (var t=0; t<ready_callbacks.length; t++) {
			ready_callbacks[t]();
		}
	</script>

</body>
</html>
