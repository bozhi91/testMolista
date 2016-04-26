@extends('layouts.web')

@section('content')

	<div id="home">

		@if ( count($properties) > 0 )
			<?php
				$main_property = $properties->shift()
			?>
			<div class="main-property carousel slide" data-interval="false">
				<div class="carousel-inner" role="listbox">
					<div class="item active" style="background-image: url('{{$main_property->main_image}}');">
						<img src="{{$main_property->main_image}}" alt="{{$main_property->title}}" class="hide" />
						<div class="carousel-caption">
							<a href="{{ action('Web\PropertiesController@details', $main_property->slug) }}" class="carousel-caption-text">
								{{$main_property->title}}
								<span class="text-nowrap hidden-xs"> | {{ price($main_property->price, [ 'decimals'=>0 ]) }}</span>
							</a>
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
												</div></div><div class="item"><div class="row">
											@endif
											<div class="col-xs-12 col-sm-4">
												<div class="relative">
													@include('web.properties.pill', [ 'item'=>$property])
													<a class="left carousel-control hide visible-xs" href="#properties-slider" role="button" data-slide="prev">
														&lsaquo;
														<span class="sr-only">{{ Lang::get('pagination.previous') }}</span>
													</a>
													<a class="right carousel-control hide visible-xs" href="#properties-slider" role="button" data-slide="next">
														&rsaquo;
														<span class="sr-only">{{ Lang::get('pagination.next') }}</span>
													</a>
												</div>
											</div>
										@endforeach
									</div>
								</div>
							</div>
							<a class="left carousel-control hide hidden-xs" href="#properties-slider" role="button" data-slide="prev">
								&lsaquo;
								<span class="sr-only">{{ Lang::get('pagination.previous') }}</span>
							</a>
							<a class="right carousel-control hide hidden-xs" href="#properties-slider" role="button" data-slide="next">
								&rsaquo;
								<span class="sr-only">{{ Lang::get('pagination.next') }}</span>
							</a>
						</div>
					</div>
				</div>
			@endif

		@endif

		<div class="container">
			<div class="quick-search-area search-area {{ count($properties) ? 'under-properties' : '' }}">
				<div class="row">
					<div class="col-xs-12 col-sm-9"></div>
					<div class="col-xs-12 col-sm-3">
						<h2>{{ Lang::get('web/search.quick.title') }}</h2>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-4 hidden-xs">
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
					<div class="col-xs-12 col-sm-4 hidden-xs">
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
					<div class="col-xs-12 col-sm-3 col-sm-offset-1">
						@include('web.search.quick', [ 'no_title'=>1 ])
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

			if ( cont.find('.properties-slider .carousel-inner .item').length < 2) {
				cont.find('.carousel-control').remove();
			} else {
				cont.find('.carousel-control').removeClass('hide');
			}
		});
	</script>

@endsection
