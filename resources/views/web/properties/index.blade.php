@extends('layouts.web', [
	'menu_section' => 'properties',
])

@section('content')

	<div id="properties">

		<div class="search-area">
			<div class="container">
				<h2>{{ Lang::get('web/properties.search.results') }}</h2>
				<div class="form-area" style="opacity: 0;">
					@include('web.search.form')
					<a href="#" class="form-area-minimizer text-center"><span class="glyphicon glyphicon-menu-up" aria-hidden="true"></span></a>
				</div>
			</div>
		</div>

		<div class="results-area">
			<div class="container">
				@if ( count($properties) < 1)
					<div class="alert alert-info">{{ Lang::get('web/properties.empty') }}</div>

				@else
					<ul class="list-unstyled property-list">
						@foreach ($properties as $property)
							<li>
								<div class="property-row">
									<div class="row">
										<div class="col-xs-12 col-sm-3">
											<a href="{{ action('Web\PropertiesController@details', $property->slug) }}" class="image" style="background-image: url('{{ $property->main_image }}');">
												<img src="{{ $property->main_image }}" alt="{{$property->title}}" class="hide" />
											</a>
										</div>
										<div class="col-xs-12 col-sm-9">
											<div class="row">
												<div class="col-xs-12 col-sm-9">
													<div class="title text-bold">
														<a href="{{ action('Web\PropertiesController@details', $property->slug) }}">{{$property->title}}</a>
													</div>
													<div class="location text-italic bg-area">
														{{ implode(', ', array_filter([
															'district' => $property->district,
															'city' => $property->city->name,
															'state' => $property->state->name,
														])) }}
														<img src="{{ asset('images/properties/marker.png') }}" class="bg-icon hidden-xs" />
													</div>
													<div class="description text-italic">{!! str_limit($property->description, 150, ' <a href="'.(action('Web\PropertiesController@details', $property->slug)).'">[...]</a>') !!}</div>
												</div>
												<div class="col-xs-12 col-sm-3">
													<div class="price text-bold text-right">
														<a href="{{ action('Web\PropertiesController@details', $property->slug) }}">{{ price($property->price, [ 'decimals'=>0 ]) }}</a>
													</div>
												</div>
											</div>
											<div class="row hidden-xs">
												<div class="col-xs-12">
													<ul class="list-inline metrics">
														<li>
															<div class="text-nowrap">
																{{ number_format($property->size,0,',','.') }} m²
															</div>
														</li>
														<li>
															<div class="text-nowrap text-lowercase bg-area bg-area-rooms">
																{{ number_format($property->rooms,0,',','.') }} 
																@if ($property->rooms == 1)
																	{{ Lang::get('web/properties.more.room') }}
																@else
																	{{ Lang::get('web/properties.more.rooms') }}
																@endif
																<img src="{{ asset('images/properties/rooms.png') }}" class="bg-icon hidden-xs" />
															</div>
														</li>
														<li>
															<div class="text-nowrap text-lowercase bg-area bg-area-baths">
																{{ number_format($property->baths,0,',','.') }}
																@if ($property->baths == 1)
																	{{ Lang::get('web/properties.more.bath') }}
																@else
																	{{ Lang::get('web/properties.more.baths') }}
																@endif
																<img src="{{ asset('images/properties/baths.png') }}" class="bg-icon hidden-xs" />
															</div>
														</li>
														<li>
															<div class="text-nowrap bg-area bg-area-ratio">
																{{ number_format(round($property->price/$property->size),0,',','.') }} €/m²
																<img src="{{ asset('images/properties/ratio.png') }}" class="bg-icon hidden-xs" />
															</div>
														</li>
													</ul>
													<div class="services text-italic">
														{{ $property->services->sortBy('title')->implode('title',', ') }}
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</li>
						@endforeach
					</ul>
					<div class="pagination-area text-center">
						{!! $properties->appends( Input::except('page') )->render() !!}
					</div>
				@endif
			</div>
		</div>

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
			var cont = $('#properties');

			cont.find('.form-area').addClass('closed').css({ opacity: 1 });

			cont.on('focus', '.first-input-line input', function(e){
				cont.find('.form-area').removeClass('closed').find('select.has-select-2').select2();
			});

			cont.on('click', '.form-area-minimizer', function(e){
				e.preventDefault();
				cont.find('.form-area').addClass('closed');
			});
/*
			function onResize() {
				if ( $('#header .navbar-toggle').is(':visible') ) {
					cont.find('.form-area').appendTo( cont );
				} else {
					cont.find('.form-area').appendTo( cont.find('.form-area-container') );
				}
			}
			$(window).resize(onResize);
			onResize();
*/
		});
	</script>

@endsection
