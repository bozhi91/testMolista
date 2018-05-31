<div class="navbar-header">
	<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
		<span class="sr-only">Toggle Navigation</span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
	</button>

	<?php
    	$site = session('SiteSetup')['site_id'];
    	$width=400;
    	$height=150;
	?>

	@if($site==136)
		<div class="row">
			<div class="col-md-12">
				<a class="navbar-brand" href="{{ action('WebController@index') }}"
				   style="width:{{$width}}px; height:{{$height}}px; background-image: url('{{ empty($site_setup['logo'])
					? Theme::url('/images/logo-default.png') : $site_setup['logo'] }}');">
				</a>
			</div>
		</div>


		@else
			<a class="navbar-brand" href="{{ action('WebController@index') }}"
			   style="background-image: url('{{ empty($site_setup['logo'])
				? Theme::url('/images/logo-default.png') : $site_setup['logo'] }}');">
			</a>
	@endif
</div>