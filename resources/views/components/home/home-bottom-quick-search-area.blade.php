<?php

	if (!isset($quickSearchAreaTitleClasses) || !isset($quickSearchTagsClasses) || !isset($recentlyAddedPropertiesClasses)) {
		$quickSearchAreaTitleClasses = "col-xs-12 col-sm-12 col-md-8";
		$quickSearchTagsClasses = "col-xs-12 col-sm-6";
		$recentlyAddedPropertiesClasses = "hidden-xs hidden-sm col-md-4";
	}
	
?>

<div class="container">
	<div class="quick-search-area search-area {{ count($highlighted) ? 'under-properties' : '' }}">
		<div class="row">
			<div class="{{$quickSearchAreaTitleClasses}}">
				<h2>{{ Lang::get('web/home.categories') }}</h2>
				<div class="row">
					<div class="{{$quickSearchTagsClasses}}">
						<a href="{{ action('Web\PropertiesController@index', [ 'newly_build'=>1 ]) }}" class="quick-link quick-link-new">
							<div class="image"></div>
							<div class="text">{{ Lang::get('web/home.link.new') }}</div>
							<div class="arrow">
								<span>&rsaquo;</span>
							</div>
						</a>
					</div>
					<div class="{{$quickSearchTagsClasses}}">
						<a href="{{ action('Web\PropertiesController@index', [ 'mode'=>'rent' ]) }}" class="quick-link quick-link-rent">
							<div class="image"></div>
							<div class="text">{{ Lang::get('web/home.link.rent') }}</div>
							<div class="arrow">
								<span>&rsaquo;</span>
							</div>
						</a>
					</div>
					<div class="{{$quickSearchTagsClasses}}">
						<a href="{{ action('Web\PropertiesController@index', [ 'second_hand'=>1 ]) }}" class="quick-link quick-link-used">
							<div class="image"></div>
							<div class="text">{{ Lang::get('web/home.link.used') }}</div>
							<div class="arrow">
								<span>&rsaquo;</span>
							</div>
						</a>
					</div>
					<div class="{{$quickSearchTagsClasses}}">
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
			<div class="{{$recentlyAddedPropertiesClasses}}">
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