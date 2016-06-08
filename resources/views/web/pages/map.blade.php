@extends('layouts.web', [
	'use_google_maps' => true,
])

@section('content')

	<div id="pages">

		<div class="container">
			<div class="row">
				<div class="cols-xs-12">
					<h1>{{ $page->title }}</h1>
					<div class="body">
						{!! $page->body !!}
					</div>
				</div>
			</div>

			<p>&nbsp;</p>
			
			<div class="row">
				<div class="cols-xs-12">
					<div id="page-map" style="height: 350px;"></div>
				</div>
			</div>
		</div>

	</div>

	<script type="text/javascript">
		google.maps.event.addDomListener(window, 'load', function(){
			var mapLatLng = { lat: {{$page->configuration['map']['lat']}}, lng: {{$page->configuration['map']['lng']}} };

			var map = new google.maps.Map(document.getElementById('page-map'), {
				zoom: {{$page->configuration['map']['zoom']}},
				center: mapLatLng,
				styles: {!! Theme::config('gmaps-style') !!}
			});

			var marker = new google.maps.Marker({
				position: mapLatLng,
				map: map,
				icon: '{{ asset('images/properties/marker.png') }}'
			});
		});

		ready_callbacks.push(function(){
			var map_area = $('#pages');
		});
	</script>

@endsection
