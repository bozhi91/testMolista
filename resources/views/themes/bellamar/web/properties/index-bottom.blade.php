<?php
	$latest = \App\Site::find($site_setup['site_id'])->properties()->enabled()->with('images')->with('state')->with('city')->withTranslations()->orderBy('created_at','desc')->limit(3)->get();
?>

<div class="container">
	<div class="bottom-area">
		<div class="row">
			<div class="col-xs-12 col-sm-8">
				<h2>{{ Lang::get('web/home.categories') }}</h2>
				<div class="row">
					<div class="col-xs-12 col-md-6">
						<a href="{{ action('Web\PropertiesController@index', [ 'newly_build'=>1 ]) }}" class="quick-link quick-link-new">
							<div class="image"></div>
							<div class="text">{{ Lang::get('web/home.link.new') }}</div>
							<div class="arrow">
								<span>&rsaquo;</span>
							</div>
						</a>
						<a href="{{ action('Web\PropertiesController@index', [ 'mode'=>'rent' ]) }}" class="quick-link quick-link-rent">
							<div class="image"></div>
							<div class="text">{{ Lang::get('web/home.link.rent') }}</div>
							<div class="arrow">
								<span>&rsaquo;</span>
							</div>
						</a>
					</div>
					<div class="col-xs-12 col-md-6">
						<a href="{{ action('Web\PropertiesController@index', [ 'second_hand'=>1 ]) }}" class="quick-link quick-link-used">
							<div class="image"></div>
							<div class="text">{{ Lang::get('web/home.link.used') }}</div>
							<div class="arrow">
								<span>&rsaquo;</span>
							</div>
						</a>
						<a href="{{ action('Web\PropertiesController@index', [ 'type'=>'house' ]) }}" class="quick-link quick-link-houses">
							<div class="image"></div>
							<div class="text">{{ Lang::get('web/home.link.houses') }}</div>
							<div class="arrow">
								<span>&rsaquo;</span>
							</div>
						</a>
					</div>
				</div>
			</div>
			<div class="col-xs-12 col-sm-4">
				<div class="hidden-xs hidden-sm">
					@if ( $latest->count() )
						<h2>{{ Lang::get('web/home.recent') }}</h2>
						@foreach ($latest as $property)
							@include('web.properties.pill-small', [ 'item'=>$property ])
						@endforeach

					@endif
				</div>
			</div>
		</div>
	</div>
</div>