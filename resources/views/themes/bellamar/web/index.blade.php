@extends('layouts.web')

@section('content')

	<div id="home">

		@if ( count($properties) > 0 )
			<?php
				$main_property = $properties->shift()
			?>

			<div class="main-property carousel slide" data-interval="false">
				<div class="carousel-inner" role="listbox">
					<div data-href="{{ action('Web\PropertiesController@details', $main_property->slug) }}" class="item active cursor-pointer" style="background-image: url('{{$main_property->main_image}}');">
						<img src="{{$main_property->main_image}}" alt="{{$main_property->title}}" class="hide" />
						<div class="carousel-caption">
							<div class="container">
								<div class="row">
									<div class="col-xs-12 col-md-3 hidden-xs hidden-sm">
										<div class="relative">
											<div class="slider-quick-search cursor-default">
												@include('web.search.quick', [ 'no_title'=>1 ])
											</div>
										</div>
									</div>
									<div class="col-xs-12 col-md-6">
										<span class="carousel-caption-text">
											{{$main_property->title}}
											<span class="text-nowrap hidden-xs"> | {{ price($main_property->price, [ 'decimals'=>0 ]) }}</span>
										</span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			@if ( count($properties) > 0 )
				<div class="container">
					<div class="properties-slider-area">
						<h2>{{ Lang::get('web/home.gallery') }}</h2>
						<div class="properties-slider">
							<div class="row">
								@foreach ($properties as $key => $property)
									@if ( $key > 0 && $key%3 == 0 )
										</div><div class="row">
									@endif
									<div class="col-xs-12 col-sm-4">
										@include('web.properties.pill', [ 'item'=>$property])
									</div>
								@endforeach
							</div>
						</div>
					</div>
				</div>
			@endif

		@endif

		<div class="container">
			<div class="quick-search-area search-area {{ count($properties) ? 'under-properties' : '' }}">
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
						<div class="visible-xs visible-sm">
							<h2>{{ Lang::get('web/search.quick.title') }}</h2>
							<div class="quick-search-xs-sm-area"></div>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
			var cont = $('#home');

			cont.find('.properties-slider .property-pill').matchHeight({ byRow : false });
			cont.find('.search-area .quick-link').matchHeight({ byRow : false });

			cont.find('.main-property .slider-quick-search').css({
				bottom : ( -1 * cont.find('.main-property .carousel-caption-text').innerHeight() ) + 'px',
				opacity: 1
			});

			cont.on('click', '.main-property .slider-quick-search', function(e){
				e.stopPropagation();
			});
			cont.on('click', '.main-property .item', function(e){
				e.preventDefault();
				document.location.href = $(this).data().href;
			});

			var search_sm = cont.find('.quick-search-xs-sm-area');
			if ( search_sm.is(':visible') ) {
				$('#quick-search-form').appendTo( search_sm );
			}
		});
	</script>

@endsection
