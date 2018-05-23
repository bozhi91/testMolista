<div class="navbar-header">
	<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
		<span class="sr-only">Toggle Navigation</span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
	</button>

	<a class="navbar-brand" href="{{ action('WebController@index') }}"
	   style="max-width:100% !important; background-image: url('{{ empty($site_setup['logo']) ? Theme::url('/images/logo-default.png') : $site_setup['logo'] }}');"></a>
</div>