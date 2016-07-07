@extends('layouts.web')

@section('content')

	<div id="home">

		@if ( count($properties) > 0 )
			<?php
				$main_property = $properties->shift()
			?>

			<div class="main-property carousel slide" data-interval="false">
				<div class="carousel-inner" role="listbox">
					<div data-href="{{ action('Web\PropertiesController@details', $main_property->slug) }}" class="item active cursor-pointer">
						<img src="{{$main_property->main_image}}" alt="{{$main_property->title}}" class="main-image" />
						<div class="carousel-caption">
							<div class="row">
								<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 carousel-caption-block">
									<span class="carousel-caption-text">
										<h4 class="carousel-title">{{$main_property->title}}</h4>
										
										<h4 class="carousel-price hidden-xs">{{ price($main_property->price, [ 'decimals'=>0 ]) }}</h4>
										
										<a href="{{ action('Web\PropertiesController@details', $main_property->slug) }}" class="carousel-button">{{ Lang::get('web/properties.search.results.more') }}</a>
									</span>
								</div>

								<div class="hidden-xs hidden-sm col-md-4 col-md-offset-4 col-lg-4 col-lg-offset-4">
									<div class="relative">
										<div class="slider-quick-search cursor-default">
											@include('web.search.quick', [ 'no_title'=>1 ])
										</div>
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
						<div id="properties-slider" class="properties-slider carousel slide">
							<div class="carousel-inner" role="listbox">
								<div class="item active">
									<div class="row">
										@foreach ($properties as $key => $property)
											@if ( $key > 0 && $key%3 == 0 )
												</div>
													@if ( $key > 0 && $key%9 == 0 )
														</div>
														<div class="item">
													@endif
												<div class="row">
											@endif
											<div class="col-xs-12 col-sm-4">
												@include('web.properties.pill', [ 'item'=>$property])
											</div>
										@endforeach
									</div>
								</div>
							</div>
							@if ( count($properties) > 9 )
								<ul class="list-inline text-right properties-slider-indicators hidden-xs">
									@foreach ($properties as $key => $property)
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

			var main_property = cont.find('.main-property');
			var main_property_image = main_property.find('.main-image');
		
			main_property_image.addClass('hide');
			main_property.find('.item.active').css({ 'background-image': 'url(' + main_property_image.attr('src') + ')' })
		

		});
	</script>

@endsection
