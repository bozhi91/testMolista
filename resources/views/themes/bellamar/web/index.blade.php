<?php

	if (!isset($colperpage)) {
		$colperpage = 4;
	}

	if (!isset($showcolrows)) {
		$showcolrows = true;
	}

	$slider_breakpoint = ( 12 / $colperpage * 3);

	$widgetSlider = false; //ugly temporal
	if(!empty($site_setup['widgets']['home'])){
		foreach ($site_setup['widgets']['home'] as $widget) {
			if($widget['type'] == 'slider'){
				$widgetSlider = $widget;
			}
		}
	}
	
	$awesomeLinks = [];
	if(!empty($site_setup['widgets']['home-footer'])){
		foreach ($site_setup['widgets']['home-footer'] as $widget) {
			if($widget['type'] == 'awesome-link'){
				$awesomeLinks[] = $widget;
			}
		}
	}
?>

@extends('layouts.web')

@section('content')

	@include('web.search.home')

	<div id="home">

		@if ( $main_property )
		
			@if($widgetSlider)
				@include('common.widget-slider', ['widget' => $widgetSlider])
			@else
				<div class="main-property carousel slide" data-interval="false">
					<div class="carousel-inner" role="listbox">
						<div data-href="{{ action('Web\PropertiesController@details', $main_property->slug) }}" class="item active cursor-pointer">
							<img src="{{$main_property->main_image}}" alt="{{$main_property->title}}" class="main-image" />
							@include('web.index-caption')
						</div>
					</div>
				</div>
			@endif
		
			@if ( $highlighted->count() > 0 )
				<div class="container">
					<div class="properties-slider-area">
						<h2>{{ Lang::get('web/home.gallery') }}</h2>
						<div id="properties-slider" class="properties-slider carousel slide">
							<div class="carousel-inner" role="listbox">
								<div class="item active">
									@if ( $showcolrows )
										<div class="row">
									@endif
									@foreach ($highlighted as $key => $property)
										@if ( $key > 0 && $key%3 == 0 )
											@if ( $showcolrows )
												</div>
											@endif
											@if ( $key > 0 && $key%$slider_breakpoint == 0 )
												</div>
												<div class="item">
											@endif
											@if ( $showcolrows )
												<div class="row">
											@endif
										@endif
										<div class="col-xs-12 col-sm-{{$colperpage}}">
											@include('web.properties.pill', [ 'item'=>$property])
										</div>
									@endforeach
									@if ( $showcolrows )
										</div>
									@endif
								</div>
							</div>
							@if ( $highlighted->count() > 9 )
								<ul class="list-inline text-right properties-slider-indicators hidden-xs">
									@foreach ($highlighted as $key => $property)
										@if ( $key%9 == 0 )
											<li data-target="#properties-slider" data-slide-to="{{ $key/9 }}" class="{{ $key ? '' : 'active' }}">{{ ($key/9)+1 }}</li>
										@endif
									@endforeach
								</ul>
							@endif
						</div>
					</div>
				</div>
			@endif

		@endif

		<div class="container">
			<div class="quick-search-area search-area {{ $highlighted->count() ? 'under-properties' : '' }}">
				<div class="row">
					<div class="col-xs-12 col-sm-8">
						<h2>{{ Lang::get('web/home.categories') }}</h2>
						<div class="row">
							@if ($awesomeLinks)
								@foreach ($awesomeLinks as $linkWidget)
									<div class="col-xs-12 col-sm-6 hidden-xs">
										@include('common.widget-awesome-link', ['widget' => $linkWidget])
									</div>
								@endforeach
							@else
							
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
							@endif
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
			cont.find('.properties-slider .item').matchHeight({ byRow : false });

			cont.find('.properties-slider').on('slid.bs.carousel', function (ui) {
				cont.find('.properties-slider .item').each(function(k,v){
					if ( $(this).hasClass('active') ) {
						cont.find('.properties-slider-indicators li').eq(k).addClass('active');
					} else {
						cont.find('.properties-slider-indicators li').eq(k).removeClass('active');
					}
				});
			});

			if ( !$('body').hasClass('theme-bellamar') ) {
				cont.find('.main-property .slider-quick-search').css({
					bottom : ( -1 * cont.find('.main-property .carousel-caption-text').innerHeight() ) + 'px',
					opacity: 1
				});
			}


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

			var main_property = cont.find('.main-property');
			var main_property_image = main_property.find('.main-image');
			if ( (main_property_image.length > 0 && main_property.height() > main_property_image.height()) || $('body').hasClass('theme-white-cloud') ) {
				main_property_image.addClass('hide');
				main_property.find('.item.active').css({ 'background-image': 'url(' + main_property_image.attr('src') + ')' })
			}

		});
	</script>

@endsection
