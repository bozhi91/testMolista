@extends('layouts.web', [
	'menu_section' => 'properties',
])

@section('content')

	<div id="property">

		<div class="container">

			<div class="header">
				<div class="price text-bold text-italic pull-right">{{ price($property->price, [ 'decimals'=>0 ]) }}</div>
				<h1 class="text-bold">{{$property->title}}</h1>
				<div class="location text-italic">
					<i class="fontello-icon fontello-icon-marker hidden-xs"></i>
					{{ implode(', ', array_filter([
						'district' => $property->district,
						'city' => $property->city->name,
						'state' => $property->state->name,
					])) }}
				</div>
			</div>

			@if ( $property->images->count() > 0 )
				<div class="images-gallery">
					<div class="image-main text-center">
						<img src="{{ $property->main_image }}" alt="{{$property->title}}" class="img-responsive" id="property-main-image" />
					</div>
					@if ( $property->images->count() > 1 )
						<div id="images-carousel" class="images-carousel carousel slide" data-interval="false">
							<div class="carousel-inner" role="listbox">
								<div class="item active">
									<div class="row">
										@foreach ($property->images->sortBy('position') as $key => $image)
											@if ( $key > 0 && $key%6 < 1 )
												</div></div><div class="item"><div class="row">
											@endif
											<div class="col-xs-4 col-sm-2">
												<a href="{{ "{$property->image_folder}/{$image->image}" }}" class="image-thumb" style="background-image: url('{{ "{$property->image_folder}/{$image->image}" }}');">
													<img src="{{ "{$property->image_folder}/{$image->image}" }}" alt="{{$property->title}}" class="hide" />
												</a>
											</div>
										@endforeach
									</div>
								</div>
							</div>
							<a class="left carousel-control hide" href="#images-carousel" role="button" data-slide="prev">
								&lsaquo;
							</a>
							<a class="right carousel-control hide" href="#images-carousel" role="button" data-slide="next">
								&rsaquo;
							</a>
						</div>
					@endif
				</div>
			@endif

			<div class="details">
				<div class="row">
					<div class="cols-xs-12 col-sm-10">
						<div class="description text-italic">
							{!! nl2p($property->description) !!}
						</div>
					</div>
				</div>
				<div class="row">
					<div class="cols-xs-12 col-sm-9">
						<ul class="list-inline metrics">
							<li>
								<div class="text-nowrap">
									{{ number_format($property->size,0,',','.') }} m²
								</div>
							</li>
							<li class="text-nowrap text-lowercase has-fontello-icon">
								<i class="fontello-icon fontello-icon-table hidden-xs"></i>
								{{ number_format($property->rooms,0,',','.') }} 
								@if ($property->rooms == 1)
									{{ Lang::get('web/properties.more.room') }}
								@else
									{{ Lang::get('web/properties.more.rooms') }}
								@endif
							</li>
							<li class="text-nowrap text-lowercase has-fontello-icon">
								<i class="fontello-icon fontello-icon-shower hidden-xs"></i>
								{{ number_format($property->baths,0,',','.') }}
								@if ($property->baths == 1)
									{{ Lang::get('web/properties.more.bath') }}
								@else
									{{ Lang::get('web/properties.more.baths') }}
								@endif
							</li>
							<li class="text-nowrap has-fontello-icon">
								<i class="fontello-icon fontello-icon-coins hidden-xs"></i>
								{{ number_format(round($property->price/$property->size),0,',','.') }} €/m²
							</li>
						</ul>
					</div>
				</div>
				<div class="row">
					<div class="cols-xs-12 col-sm-9">
						<div class="services text-italic">
							{{ $property->services->sortBy('title')->implode('title',', ') }}
						</div>
					</div>
				</div>
				<a href="#" class="btn btn-primary call-to-action more-info-trigger">{{ Lang::get('web/properties.call.to.action') }}</a>
			</div>

			<div class="map-area">
				<div id="property-map" class="map"></div>
			</div>

			@include('web.properties.details-bottom', [ 'related_properties'=>$property->related_properties ])

		</div>

	</div>

	<script type="text/javascript">
		google.maps.event.addDomListener(window, 'load', function(){
			var mapLatLng = { lat: {{$property->lat}}, lng: {{$property->lng}} };

			var property_map = new google.maps.Map(document.getElementById('property-map'), {
				zoom: 14,
				center: mapLatLng,
				styles: {!! Theme::config('gmaps-style') !!}
			});

			property_marker = new google.maps.Marker({
				position: mapLatLng,
				map: property_map,
				icon: '{{ asset('images/properties/marker.png') }}'
			});
		});

		ready_callbacks.push(function(){
			var cont = $('#property');

			if ( cont.find('.images-carousel .carousel-inner .item').length < 2) {
				cont.find('.images-carousel .carousel-control').remove();
			} else {
				cont.find('.images-carousel .carousel-control').removeClass('hide');
			}

			cont.find('.image-thumb').magnificPopup({
				type: 'image',
				gallery:{
					enabled:true
				}
			});

			cont.on('click', '.more-info-trigger', function(e){
				e.preventDefault();
				alert('[TODO] What should this button do?');
			});

			cont.find('.bottom-links .property-pill').matchHeight({ byRow : false });
		});
	</script>

@endsection
