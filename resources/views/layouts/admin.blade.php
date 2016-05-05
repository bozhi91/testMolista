<!DOCTYPE html>
<html lang="{{ LaravelLocalization::getCurrentLocale() }}" dir="{{ LaravelLocalization::getCurrentLocaleDirection() }}">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>{{ empty($page_title) ? 'Molista' : $page_title }}</title>

	<link href="{{ Theme::url('/compiled/css/admin.css') }}" rel="stylesheet" type='text/css' />

	<link id="page_favicon" href="{{ Theme::url('/favicon.ico') }}" rel="icon" type="image/x-icon" />

	<script type="text/javascript">
		var ready_callbacks = [];
	</script>
</head>

<body>

<nav class="navbar navbar-default">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
				<span class="sr-only">Toggle Navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="{{ action('AdminController@index') }}">{{ Lang::get('admin/menu.home') }}</a>
		</div>

		<div class="collapse navbar-collapse" id="app-navbar-collapse">
			<ul class="nav navbar-nav navbar-right">
				@permission('site-*')
					<li><a href="{{ action('Admin\SitesController@index') }}">{{ Lang::get('admin/menu.sites') }}</a></li>
				@endpermission
				@permission('user-*')
					<li><a href="{{ action('Admin\UsersController@index') }}">{{ Lang::get('admin/menu.users') }}</a></li>
				@endpermission
				@permission('property-*')
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ Lang::get('admin/menu.properties') }} <span class="caret"></span></a>
						<ul class="dropdown-menu">
							<li><a href="{{ action('Admin\Properties\BaseController@index') }}">{{ Lang::get('admin/menu.list') }}</a></li>
							@permission('property-service')
								<li><a href="{{ action('Admin\Properties\ServicesController@index') }}">{{ Lang::get('admin/menu.services') }}</a></li>
							@endpermission
						</ul>
					</li>
				@endpermission
				@if ( Auth::user()->can('locale-*') || Auth::user()->can('translation-*') )
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ Lang::get('admin/menu.configuration') }} <span class="caret"></span></a>
						<ul class="dropdown-menu">
							@if ( Auth::user()->can('translation-*') )
								<li><a href="{{ action('Admin\Config\TranslationsController@index') }}">{{ Lang::get('admin/menu.translations') }}</a></li>
							@endif
							@if ( Auth::user()->can('locale-*') )
								<li><a href="{{ action('Admin\Config\LocalesController@index') }}">{{ Lang::get('admin/menu.locales') }}</a></li>
							@endif
							@if ( Auth::user()->can('geography-*') )
								@if ( Auth::user()->can('locale-*') || Auth::user()->can('translation-*') )
									<li role="separator" class="divider"></li>
								@endif
								<li><a href="#" onclick="alert('[TODO]'); return false;">{{ Lang::get('admin/menu.geography') }}</a></li>
							@endif
						</ul>
					</li>
				@endif
				<li><a href="{{ action('Auth\AuthController@logout') }}">{{ Lang::get('admin/menu.logout') }}</a></li>
			</ul>
		</div>
	</div>
</nav>

<footer class="footer">
	<div class="container">
		<ul class="list-unstyled pull-left footer-list">
			@role('admin')
				<li>
					<a href="{{ action('\Rap2hpoutre\LaravelLogViewer\LogViewerController@index') }}" class="btn btn-danger" target="_blank">
						<span class="glyphicon glyphicon-new-window" aria-hidden="true"></span>
						Error log
					</a>
				</li>
			@endrole
		</ul>
		<ul class="list-unstyled pull-right footer-list">
			<li>
				<select onchange="document.location.href=this.value;" class="form-control">
					@foreach(LaravelLocalization::getSupportedLocales() as $locale => $def)
						<option value="{{ LaravelLocalization::getLocalizedURL($locale) }}" {!! ( $locale == LaravelLocalization::getCurrentLocale() ) ? 'selected="selected"' : '' !!}>{{ $def['native'] }}</option>
					@endforeach
				</select>
			</li>
		</ul>
	</div>
</footer>

@yield('content')

<script src="{{ Theme::url('/compiled/js/admin.js') }}"></script>
<script src="{{ Theme::url('/js/jquery.validate/messages_' . LaravelLocalization::getCurrentLocale() . '.min.js') }}"></script>
<script src="{{ Theme::url('/js/alertify/messages_' . LaravelLocalization::getCurrentLocale() . '.js') }}"></script>
<script src="{{ Theme::url('/js/select2/' . LaravelLocalization::getCurrentLocale() . '.js') }}"></script>

</body>
</html>
