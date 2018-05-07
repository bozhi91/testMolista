<nav class="navbar navbar-default">

	<div class="container">

		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
				<span class="sr-only">Toggle Navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="{{ action('CorporateController@index') }}">Molista</a>
		</div>

		<div class="collapse navbar-collapse" id="app-navbar-collapse">
			<ul class="nav navbar-nav navbar-right">
				<li class="visible-xs language-list">
					{{ Lang::get('web/header.lang') }}:
					@foreach(LaravelLocalization::getSupportedLocales() as $locale => $def)
						<a href="{{ LaravelLocalization::getLocalizedURL($locale) }}" class="normal {!! ( $locale == LaravelLocalization::getCurrentLocale() ) ? 'current' : '' !!}" title="{{ $def['native'] }}">{{ $locale }}</a>
						<span class="sep">/</span>
					@endforeach
				</li>
				@if ( Auth::guest() )
					<li><a href="{{ action('Auth\AuthController@login') }}">{{ Lang::get('web/header.login') }}</a></li>
				@else
					<li><a href="{{ action('AdminController@index') }}">{{ Lang::get('admin/menu.home') }}</a></li>
					<li><a href="{{ action('Auth\AuthController@logout') }}">{{ Lang::get('web/header.logout') }}</a></li>
				@endif
			</ul>
		</div>
	</div>
</nav>